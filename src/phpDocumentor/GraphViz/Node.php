<?php

declare(strict_types=1);

/**
 * phpDocumentor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\GraphViz;

use function addslashes;
use function implode;
use function strtolower;
use function substr;

/**
 * Class representing a node / element in a graph.
 *
 * @link      http://phpdoc.org
 *
 * @method void setLabel(string $name) Sets the label for this node.
 *
 * @psalm-suppress ClassMustBeFinal
 */
class Node implements \Stringable
{
    use Attributes;

    /** Name for this node */
    protected string $name = '';

    /**
     * Creates a new node with name and optional label.
     *
     * @param string      $name  Name of the new node.
     * @param string|null $label Optional label text.
     */
    public function __construct(string $name, ?string $label = null)
    {
        $this->setName($name);
        if ($label === null) {
            return;
        }

        $this->setLabel($label);
    }

    /**
     * Factory method used to assist with fluent interface handling.
     *
     * See the examples for more details.
     *
     * @param string      $name  Name of the new node.
     * @param string|null $label Optional label text.
     */
    public static function create(string $name, ?string $label = null): self
    {
        return new self($name, $label);
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

    /**
     * Returns the name for this node.
     */
    public function getName(): string
    {
        return $this->name;
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

        $attributes = implode("\n", $attributes);

        $name = addslashes($this->getName());

        return <<<DOT
"{$name}" [
{$attributes}
]
DOT;
    }
}
