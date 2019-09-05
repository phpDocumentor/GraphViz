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
 * Class representing a node / element in a graph.
 *
 * @see      http://phpdoc.org
 *
 * @method void setLabel(string $name) Sets the label for this node.
 */
class Node implements AttributesAwareInterface, GraphAwareInterface
{
    use AttributesAware;
    use GraphAware;

    /** @var string Name for this node */
    protected $name = '';

    /**
     * Creates a new node with name and optional label.
     *
     * @param string      $name  Name of the new node.
     * @param null|string $label Optional label text.
     */
    public function __construct(string $name, ?string $label = null)
    {
        $this->setName($name);

        if (null === $label) {
            return;
        }

        $this->setLabel($label);
    }

    /**
     * Magic method to provide a getter/setter to add attributes on the Node.
     *
     * Using this method we make sure that we support any attribute without
     * too much hassle. If the name for this method does not start with get or
     * set we return null.
     *
     * Set methods return this graph (fluent interface) whilst get methods
     * return the attribute value.
     *
     * @param string  $name      Method name; either getX or setX is expected.
     * @param mixed[] $arguments List of arguments; only 1 is expected for setX.
     *
     * @throws AttributeNotFound
     *
     * @return null|Attribute|Node
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
     * Returns the node definition as is requested by GraphViz.
     */
    public function __toString(): string
    {
        $attributes = [];

        foreach ($this->attributes as $value) {
            $attributes[] = (string) $value;
        }

        $attributes = \implode("\n", $attributes);

        $name = \addslashes($this->getName());

        return <<<DOT
"{$name}" [
{$attributes}
]
DOT;
    }

    /**
     * Factory method used to assist with fluent interface handling.
     *
     * See the examples for more details.
     *
     * @param string      $name  Name of the new node.
     * @param null|string $label Optional label text.
     */
    public static function create(string $name, ?string $label = null): self
    {
        return new self($name, $label);
    }

    /**
     * Returns the name for this node.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name for this node.
     *
     * Not to confuse with the label.
     *
     * @param string $name Name for this node.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
