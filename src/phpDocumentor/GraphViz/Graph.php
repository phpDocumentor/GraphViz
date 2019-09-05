<?php

declare(strict_types=1);

/**
 * phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see      http://phpdoc.org
 */

namespace phpDocumentor\GraphViz;

use const DIRECTORY_SEPARATOR;
use InvalidArgumentException;
use const PHP_EOL;
use phpDocumentor\GraphViz\Contract\AttributesAwareInterface;

/**
 * Class representing a graph; this may be a main graph but also a subgraph.
 *
 * In case of a subgraph:
 * When the name of the subgraph is prefixed with _cluster_ then the contents
 * of this graph will be grouped and a border will be added. Otherwise it is
 * used as logical container to place defaults in.
 *
 * @method Graph setRankSep(string $rankSep)
 * @method Graph setCenter(string $center)
 * @method Graph setRank(string $rank)
 * @method Graph setRankDir(string $rankDir)
 * @method Graph setSplines(string $splines)
 * @method Graph setConcentrate(string $concentrate)
 */
class Graph implements AttributesAwareInterface
{
    use AttributesAware;

    /** @var Edge[] A list of edges / arrows for this Graph */
    protected $edges = [];

    /** @var Graph[] A list of subgraphs for this Graph */
    protected $graphs = [];

    /** @var string Name of this graph */
    protected $name = 'G';

    /** @var Node[] A list of nodes for this Graph */
    protected $nodes = [];

    /** @var string The path to execute dot from */
    protected $path = '';

    /** @var bool If the graph is strict then multiple edges are not allowed between the same pairs of nodes */
    protected $strict = false;

    /** @var string Type of this graph; may be digraph, graph or subgraph */
    protected $type = 'digraph';

    /**
     * Magic method to provide a getter/setter to add attributes on the Graph.
     *
     * Using this method we make sure that we support any attribute without
     * too much hassle. If the name for this method does not start with get
     * or set we return null.
     *
     * Set methods return this graph (fluent interface) whilst get methods
     * return the attribute value.
     *
     * @param string  $name      Name of the method including get/set
     * @param mixed[] $arguments The arguments, should be 1: the value
     *
     * @throws AttributeNotFound
     *
     * @return null|Attribute|Graph
     */
    public function __call(string $name, array $arguments)
    {
        $key = \mb_strtolower(\mb_substr($name, 3));

        if (0 === \mb_stripos($name, 'set')) {
            return $this->setAttribute($key, (string) $arguments[0]);
        }

        if (0 === \mb_stripos($name, 'get')) {
            return $this->getAttribute($key);
        }
    }

    /**
     * Returns the requested node by its name.
     *
     * @see Graph::setNode()
     *
     * @param string $name The name of the node to retrieve.
     */
    public function __get(string $name): ?Node
    {
        return $this->nodes[$name] ?? null;
    }

    /**
     * Sets a node using a custom name.
     *
     * @see Graph::setNode()
     *
     * @param string $name  Name of the node.
     * @param Node   $value Node to set on the given name.
     */
    public function __set(string $name, Node $value): self
    {
        $this->nodes[$name] = $value;

        return $this;
    }

    /**
     * Generates a DOT file for use with GraphViz.
     *
     * GraphViz is not used in this method; it is safe to call it even without
     * GraphViz installed.
     */
    public function __toString(): string
    {
        $elements = \array_merge(
            $this->graphs,
            $this->attributes,
            $this->edges,
            $this->nodes
        );

        $attributes = [];

        foreach ($elements as $value) {
            $attributes[] = (string) $value;
        }

        $attributes = \implode(PHP_EOL, $attributes);

        $strict = ($this->isStrict() ? 'strict ' : '');

        return <<<DOT
{$strict}{$this->getType()} "{$this->getName()}" {
{$attributes}
}
DOT;
    }

    /**
     * Adds a subgraph to this graph; automatically changes the type to subgraph.
     *
     * Please note that an index is maintained using the name of the subgraph.
     * Thus if you have 2 subgraphs with the same name that the first will be
     * overwritten by the latter.
     *
     * @see Graph::create()
     *
     * @param Graph $graph The graph to add onto this graph as
     * subgraph.
     */
    public function addGraph(self $graph): self
    {
        $graph->setType('subgraph');
        $this->graphs[$graph->getName()] = $graph;

        return $this;
    }

    /**
     * Factory method to instantiate a Graph so that you can use fluent coding
     * to chain everything.
     *
     * @param string $name        The name for this graph.
     * @param bool   $directional Whether this is a directed or undirected graph.
     *
     * @return \phpDocumentor\GraphViz\Graph
     */
    public static function create(string $name = 'G', bool $directional = true): self
    {
        $graph = new self();
        $graph
            ->setName($name)
            ->setType($directional ? 'digraph' : 'graph');

        return $graph;
    }

    /**
     * Exports this graph to a generated image.
     *
     * This is the only method that actually requires GraphViz.
     *
     * @see http://www.graphviz.org/content/output-formats
     *
     * @uses GraphViz/dot
     *
     * @param string $type     The type to export to; see the link above for a
     *     list of supported types.
     * @param string $filename The path to write to.
     *
     * @throws Exception If an error occurred in GraphViz.
     */
    public function export(string $type, string $filename): self
    {
        $type = \escapeshellarg($type);
        $filename = \escapeshellarg($filename);

        // write the dot file to a temporary file
        $tmpfile = (string) \tempnam(\sys_get_temp_dir(), 'gvz');
        \file_put_contents($tmpfile, (string) $this);

        // escape the temp file for use as argument
        $tmpfileArg = \escapeshellarg($tmpfile);

        // create the dot output
        $output = [];
        $code = 0;
        \exec($this->path . "dot -T{$type} -o{$filename} < {$tmpfileArg} 2>&1", $output, $code);
        \unlink($tmpfile);

        if (0 !== $code) {
            throw new Exception(
                'An error occurred while creating the graph; GraphViz returned: '
                . \implode(PHP_EOL, $output)
            );
        }

        return $this;
    }

    /**
     * Finds a node in this graph or any of its subgraphs.
     *
     * @param string $name Name of the node to find.
     */
    public function findNode(string $name): ?Node
    {
        if (isset($this->nodes[$name])) {
            return $this->nodes[$name];
        }

        foreach ($this->graphs as $graph) {
            $node = $graph->findNode($name);

            if ($node) {
                return $node;
            }
        }

        return null;
    }

    /**
     * Returns the subgraph with a given name.
     *
     * @param string $name Name of the requested graph.
     */
    public function getGraph(string $name): self
    {
        return $this->graphs[$name];
    }

    /**
     * Returns the name for this Graph.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the type of this Graph.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Checks whether a graph with a certain name already exists.
     *
     * @param string $name Name of the graph to find.
     */
    public function hasGraph(string $name): bool
    {
        return isset($this->graphs[$name]);
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Links two nodes to eachother and registers the Edge onto this graph.
     *
     * @see Edge::create()
     *
     * @param Edge $edge The link between two classes.
     */
    public function link(Edge $edge): self
    {
        $edge->setGraphRoot($this);

        $this->edges[] = $edge;

        return $this;
    }

    /**
     * Sets the name for this graph.
     *
     * If this is a subgraph you can prefix the name with _cluster_ to group all
     * contained nodes and add a border.
     *
     * @param string $name The new name for this graph.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets a node in the $nodes array; uses the name of the node as index.
     *
     * Nodes can be retrieved by retrieving the property with the same name.
     * Thus 'node1' can be retrieved by invoking: $graph->node1
     *
     * @see Node::create()
     *
     * @param Node $node The node to set onto this Graph.
     */
    public function setNode(Node $node): self
    {
        $node->setGraphRoot($this);

        $this->nodes[$node->getName()] = $node;

        return $this;
    }

    /**
     * Sets the path for the execution. Only needed if it is not in the PATH env.
     *
     * @param string $path The path to execute dot from
     */
    public function setPath(string $path): self
    {
        $realpath = \realpath($path);

        if ($path && $path === $realpath) {
            $this->path = $path . DIRECTORY_SEPARATOR;
        }

        return $this;
    }

    /**
     * Set if the Graph should be strict. If the graph is strict then
     * multiple edges are not allowed between the same pairs of nodes.
     *
     * @param bool $isStrict
     */
    public function setStrict(bool $isStrict): self
    {
        $this->strict = $isStrict;

        return $this;
    }

    /**
     * Sets the type for this graph.
     *
     * @param string $type Must be either "digraph", "graph" or "subgraph".
     *
     * @throws InvalidArgumentException If $type is not "digraph", "graph" or
     * "subgraph".
     */
    public function setType(string $type): self
    {
        if (!\in_array($type, ['digraph', 'graph', 'subgraph'], true)) {
            throw new InvalidArgumentException(
                'The type for a graph must be either "digraph", "graph" or '
                . '"subgraph"'
            );
        }

        $this->type = $type;

        return $this;
    }
}
