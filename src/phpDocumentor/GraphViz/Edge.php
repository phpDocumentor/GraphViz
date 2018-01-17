<?php
declare(strict_types=1);

/**
 * phpDocumentor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\GraphViz;

/**
 * Class representing an edge (arrow, line).
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
class Edge
{
    /** @var Node Node from where to link */
    protected $from = null;

    /** @var Node Node where to to link */
    protected $to = null;

    /** @var Attribute[] List of attributes for this edge */
    protected $attributes = [];

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
     * Factory method used to assist with fluent interface handling.
     *
     * See the examples for more details.
     *
     * @param Node $from Starting node to create an Edge from.
     * @param Node $to Destination node where to create and
     * edge to.
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
     *  setX or getX.
     * @param mixed[] $arguments Arguments for the setter, only 1 is expected: value
     *
     * @return Attribute|Edge|null
     */
    public function __call(string $name, array $arguments)
    {
        $key = strtolower(substr($name, 3));
        if (strtolower(substr($name, 0, 3)) === 'set') {
            $this->attributes[$key] = new Attribute($key, (string) $arguments[0]);

            return $this;
        }

        if (strtolower(substr($name, 0, 3)) === 'get') {
            return $this->attributes[$key];
        }

        return null;
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

        $attributes = implode("\n", $attributes);

        $from_name = addslashes($this->getFrom()->getName());
        $to_name = addslashes($this->getTo()->getName());

        return <<<DOT
"${from_name}" -> "${to_name}" [
${attributes}
]
DOT;
    }
}
