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

namespace phpDocumentor\GraphViz\Test;

use Mockery as m;
use phpDocumentor\GraphViz\AttributeNotFound;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Node;
use PHPUnit\Framework\TestCase;

/**
 * Test for the the class representing a GraphViz edge (vertex).
 *
 * @internal
 * @coversNothing
 */
final class EdgeTest extends TestCase
{
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Tests the magic __call method, to work as described, return the object
     * instance for a setX method, return the value for an getX method, and null
     * for the remaining method calls.
     *
     * @covers \phpDocumentor\GraphViz\Edge::__call
     * @covers \phpDocumentor\GraphViz\Edge::getAttribute
     * @covers \phpDocumentor\GraphViz\Edge::setAttribute
     */
    public function testCall(): void
    {
        $label = 'my label';
        $fixture = new Edge(new Node('from'), new Node('to'));
        self::assertInstanceOf(Edge::class, $fixture->setLabel($label));
        self::assertSame($label, $fixture->getLabel()->getValue());
        self::assertNull($fixture->someNonExcistingMethod());
    }

    /**
     * Tests the construct method.
     *
     * @covers \phpDocumentor\GraphViz\Edge::__construct
     */
    public function testConstruct(): void
    {
        $fromNode = m::mock(Node::class);
        $toNode = m::mock(Node::class);
        $fixture = new Edge($fromNode, $toNode);

        self::assertInstanceOf(
            Edge::class,
            $fixture
        );
        self::assertSame(
            $fromNode,
            $fixture->getFrom()
        );
        self::assertSame(
            $toNode,
            $fixture->getTo()
        );
    }

    /**
     * Tests the create method.
     *
     * @covers \phpDocumentor\GraphViz\Edge::create
     */
    public function testCreate(): void
    {
        self::assertInstanceOf(
            Edge::class,
            Edge::create(new Node('from'), new Node('to'))
        );
    }

    /**
     * Tests whether the getFrom method returns the same node as passed
     * in the create method.
     *
     * @covers \phpDocumentor\GraphViz\Edge::getFrom
     */
    public function testGetFrom(): void
    {
        $from = new Node('from');
        $edge = Edge::create($from, new Node('to'));
        self::assertSame($from, $edge->getFrom());
    }

    /**
     * @covers \phpDocumentor\GraphViz\AttributeNotFound::__construct
     * @covers \phpDocumentor\GraphViz\Edge::getAttribute
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound(): void
    {
        $fixture = new Edge(new Node('from'), new Node('to'));

        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "label" was not found');

        $fixture->getLabel();
    }

    /**
     * Tests the getTo method returns the same node as passed
     * in the create method.
     *
     * @covers \phpDocumentor\GraphViz\Edge::getTo
     */
    public function testGetTo(): void
    {
        $to = new Node('to');
        $edge = Edge::create(new Node('from'), $to);
        self::assertSame($to, $edge->getTo());
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard.
     *
     * @covers \phpDocumentor\GraphViz\Edge::__toString
     */
    public function testToString(): void
    {
        $fixture = new Edge(new Node('from'), new Node('to'));
        $fixture->setLabel('MyLabel');
        $fixture->setWeight(45);

        $dot = <<<'DOT'
"from" -- "to" [
label="MyLabel"
weight="45"
]
DOT;

        self::assertSame($dot, (string) $fixture);
    }
}
