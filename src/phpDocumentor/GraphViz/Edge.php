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

use phpDocumentor\GraphViz\Contract\AttributesAwareInterface;
use phpDocumentor\GraphViz\Contract\GraphAwareInterface;

/**
 * Class representing an edge (arrow, line).
 *
 * @see      http://phpdoc.org
 */
class Edge implements AttributesAwareInterface, GraphAwareInterface
{
    use AttributesAware;
    use GraphAware;

    /** @var Node Node from where to link */
    private $from;

    /** @var Node Node where to to link */
    private $to;

    /**
     * Creates a new Edge / Link between the given nodes.
     *
     * @param Node $from Starting node to create an Edge from.
     * @param Node $to   Destination node where to create and
     *  edge to.
     */
    public function __construct(Node $from, Node $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Magic method to provide a getter/setter to add attributes on the edge.
     *
     * Using this method we make sure that we support any attribute without too
     * much hassle. If the name for this method does not start with get or set
     * we return null.
     *
     * Set methods return this graph (fluent interface) whilst get methods
     * return the attribute value.
     *
     * @param string  $name      name of the invoked method, expect it to be
     *       setX or getX.
     * @param mixed[] $arguments Arguments for the setter, only 1 is expected: value
     *
     * @throws AttributeNotFound
     *
     * @return null|Attribute|Edge
     */
    public function __call(string $name, array $arguments)
    {
        $key = \mb_strtolower(\mb_substr($name, 3));

        if ('set' === \mb_strtolower(\mb_substr($name, 0, 3))) {
            return $this->setAttribute($key, (string) $arguments[0]);
        }

        if ('get' === \mb_strtolower(\mb_substr($name, 0, 3))) {
            return $this->getAttribute($key);
        }
    }

    /**
     * Returns the edge definition as is requested by GraphViz.
     */
    public function __toString(): string
    {
        $attributes = [];

        foreach ($this->attributes as $value) {
            $attributes[] = (string) $value;
        }

        $attributes = \implode("\n", $attributes);

        $from_name = \addslashes($this->getFrom()->getName());
        $to_name = \addslashes($this->getTo()->getName());

        $direction = '--';

        if (null !== $graph = $this->getGraphRoot()) {
            if ('digraph' === $graph->getType()) {
                $direction = '->';
            }
        }

        return <<<DOT
"{$from_name}" {$direction} "{$to_name}" [
{$attributes}
]
DOT;
    }

    /**
     * Factory method used to assist with fluent interface handling.
     *
     * See the examples for more details.
     *
     * @param Node $from Starting node to create an Edge from.
     * @param Node $to   Destination node where to create and
     *   edge to.
     */
    public static function create(Node $from, Node $to): self
    {
        return new self($from, $to);
    }

    /**
     * Returns the source Node for this Edge.
     */
    public function getFrom(): Node
    {
        return $this->from;
    }

    /**
     * Returns the destination Node for this Edge.
     */
    public function getTo(): Node
    {
        return $this->to;
    }
}
