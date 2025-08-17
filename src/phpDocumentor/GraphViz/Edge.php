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
 * Class representing an edge (arrow, line).
 *
 * @link      http://phpdoc.org
 *
 * @psalm-suppress ClassMustBeFinal
 */
class Edge implements \Stringable
{
    use Attributes;

    /** Node from where to link */
    private Node $from;

    /** Node where to link */
    private Node $to;

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
        $this->to   = $to;
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

        $fromName = addslashes($this->getFrom()->getName());
        $toName   = addslashes($this->getTo()->getName());

        return <<<DOT
"{$fromName}" -> "{$toName}" [
{$attributes}
]
DOT;
    }
}
