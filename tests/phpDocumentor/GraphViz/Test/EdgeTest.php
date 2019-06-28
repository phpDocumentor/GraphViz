<?php
/**
 * phpDocumentor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   phpDocumentor\GraphViz\Test
 * @author    Danny van der Sluijs <danny.vandersluijs@fleppuhstein.com>
 * @copyright 2012-2018 Danny van der Sluijs (http://www.dannyvandersluijs.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
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
 * @package phpDocumentor\GraphViz\Test
 * @author  Danny van der Sluijs <danny.vandersluijs@fleppuhstein.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class EdgeTest extends TestCase
{
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * Tests the construct method
     *
     * @covers \phpDocumentor\GraphViz\Edge::__construct
     */
    public function testConstruct()
    {
        $fromNode = m::mock(Node::class);
        $toNode = m::mock(Node::class);
        $fixture = new Edge($fromNode, $toNode);

        $this->assertInstanceOf(
            Edge::class,
            $fixture
        );
        $this->assertSame(
            $fromNode,
            $fixture->getFrom()
        );
        $this->assertSame(
            $toNode,
            $fixture->getTo()
        );
    }

    /**
     * Tests the create method
     *
     * @covers \phpDocumentor\GraphViz\Edge::create
     */
    public function testCreate()
    {
        $this->assertInstanceOf(
            Edge::class,
            Edge::create(new Node('from'), new Node('to'))
        );
    }

    /**
     * Tests whether the getFrom method returns the same node as passed
     * in the create method
     *
     * @covers \phpDocumentor\GraphViz\Edge::getFrom
     */
    public function testGetFrom()
    {
        $from = new Node('from');
        $edge = Edge::create($from, new Node('to'));
        $this->assertSame($from, $edge->getFrom());
    }

    /**
     * Tests the getTo method returns the same node as passed
     * in the create method
     *
     * @covers \phpDocumentor\GraphViz\Edge::getTo
     */
    public function testGetTo()
    {
        $to = new Node('to');
        $edge = Edge::create(new Node('from'), $to);
        $this->assertSame($to, $edge->getTo());
    }

    /**
     * Tests the magic __call method, to work as described, return the object
     * instance for a setX method, return the value for an getX method, and null
     * for the remaining method calls
     *
     * @covers \phpDocumentor\GraphViz\Edge::__call
     * @covers \phpDocumentor\GraphViz\Edge::setAttribute
     * @covers \phpDocumentor\GraphViz\Edge::getAttribute
     */
    public function testCall()
    {
        $label = 'my label';
        $fixture = new Edge(new Node('from'), new Node('to'));
        $this->assertInstanceOf(Edge::class, $fixture->setLabel($label));
        $this->assertSame($label, $fixture->getLabel()->getValue());
        $this->assertNull($fixture->someNonExcistingMethod());
    }

    /**
     * @covers \phpDocumentor\GraphViz\Edge::getAttribute
     * @covers \phpDocumentor\GraphViz\AttributeNotFound::__construct
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound()
    {
        $fixture = new Edge(new Node('from'), new Node('to'));

        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "label" was not found');

        $fixture->getLabel();
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard
     *
     * @covers \phpDocumentor\GraphViz\Edge::__toString
     */
    public function testToString()
    {
        $fixture = new Edge(new Node('from'), new Node('to'));
        $fixture->setLabel('MyLabel');
        $fixture->setWeight(45);

        $dot = <<<DOT
"from" -> "to" [
label="MyLabel"
weight="45"
]
DOT;

        $this->assertSame($dot, (string) $fixture);
    }
}
