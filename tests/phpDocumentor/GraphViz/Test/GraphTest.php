<?php
/**
 * phpDocumentor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\GraphViz\Test;

use Mockery as m;
use phpDocumentor\GraphViz\AttributeNotFound;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use PHPUnit\Framework\TestCase;

/**
 * Test for the the class representing a GraphViz graph.
 */
class GraphTest extends TestCase
{
    /**
     * @var Graph
     */
    protected $fixture;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->fixture = new Graph();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::create
     */
    public function testCreate()
    {
        $fixture = Graph::create();
        $this->assertInstanceOf(
            Graph::class,
            $fixture
        );
        $this->assertSame(
            'G',
            $fixture->getName()
        );
        $this->assertSame(
            'digraph',
            $fixture->getType()
        );

        $fixture = Graph::create('MyName', false);
        $this->assertSame(
            'MyName',
            $fixture->getName()
        );
        $this->assertSame(
            'graph',
            $fixture->getType()
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::setName
     */
    public function testSetName()
    {
        $this->assertSame(
            $this->fixture,
            $this->fixture->setName('otherName'),
            'Expecting a fluent interface'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getName
     */
    public function testGetName()
    {
        $this->assertSame(
            $this->fixture->getName(),
            'G',
            'Expecting the name to match the initial state'
        );
        $this->fixture->setName('otherName');
        $this->assertSame(
            $this->fixture->getName(),
            'otherName',
            'Expecting the name to contain the new value'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::setType
     */
    public function testSetType()
    {
        $this->assertSame(
            $this->fixture,
            $this->fixture->setType('digraph'),
            'Expecting a fluent interface'
        );
        $this->assertSame(
            $this->fixture,
            $this->fixture->setType('graph'),
            'Expecting a fluent interface'
        );
        $this->assertSame(
            $this->fixture,
            $this->fixture->setType('subgraph'),
            'Expecting a fluent interface'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::setType
     */
    public function testSetTypeException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fixture->setType('fakegraphg');
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getType
     */
    public function testGetType()
    {
        $this->assertSame(
            $this->fixture->getType(),
            'digraph'
        );
        $this->fixture->setType('graph');
        $this->assertSame(
            $this->fixture->getType(),
            'graph'
        );
    }

    public function testSetStrict()
    {
        $this->assertSame(
            $this->fixture,
            $this->fixture->setStrict(true),
            'Expecting a fluent interface'
        );
        $this->assertSame(
            $this->fixture,
            $this->fixture->setStrict(false),
            'Expecting a fluent interface'
        );
    }

    public function testIsStrict()
    {
        $this->assertSame(
            $this->fixture->isStrict(),
            false
        );
        $this->fixture->setStrict(true);
        $this->assertSame(
            $this->fixture->isStrict(),
            true
        );
    }

    public function testSetPath()
    {
        $this->assertSame(
            $this->fixture,
            $this->fixture->setPath(__DIR__),
            'Expecting a fluent interface'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__call
     * @covers \phpDocumentor\GraphViz\Graph::getAttribute
     * @covers \phpDocumentor\GraphViz\Graph::setAttribute
     */
    public function test__call()
    {
        $this->assertNull($this->fixture->MyMethod());
        $this->assertSame($this->fixture, $this->fixture->setColor('black'));
        $this->assertSame('black', $this->fixture->getColor()->getValue());
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getAttribute
     * @covers \phpDocumentor\GraphViz\AttributeNotFound::__construct
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound()
    {
        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "color" was not found');

        $this->fixture->getColor();
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::addGraph
     */
    public function testAddGraph()
    {
        $mock = m::mock(Graph::class);
        $mock->expects('setType');
        $mock->expects('getName');

        $this->assertSame(
            $this->fixture,
            $this->fixture->addGraph($mock)
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::hasGraph
     */
    public function testHasGraph()
    {
        $mock = m::mock(Graph::class);
        $mock->expects('getName')->andReturn('MyName');
        $mock->expects('setType');

        $this->assertFalse($this->fixture->hasGraph('MyName'));
        $this->fixture->addGraph($mock);
        $this->assertTrue($this->fixture->hasGraph('MyName'));
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getGraph
     */
    public function testGetGraph()
    {
        $mock = m::mock(Graph::class);
        $mock->expects('setType');
        $mock->expects('getName')->andReturn('MyName');

        $this->fixture->addGraph($mock);
        $this->assertSame(
            $mock,
            $this->fixture->getGraph('MyName')
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::setNode
     */
    public function testSetNode()
    {
        $mock = m::mock(Node::class);
        $mock->expects('getName')->andReturn('MyName');

        $this->assertSame(
            $this->fixture,
            $this->fixture->setNode($mock)
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::findNode
     */
    public function testFindNode()
    {
        $this->assertNull($this->fixture->findNode('MyNode'));

        $mock = m::mock(Node::class);
        $mock->expects('getName')->andReturn('MyName');

        $this->fixture->setNode($mock);
        $this->assertSame(
            $mock,
            $this->fixture->findNode('MyName')
        );

        $subGraph = Graph::create();
        $mock2 = m::mock(Node::class);
        $mock2->expects('getName')->andReturn('MyName2');

        $subGraph->setNode($mock2);

        $this->fixture->addGraph($subGraph);
        $this->assertSame(
            $mock2,
            $this->fixture->findNode('MyName2')
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__set
     */
    public function test__set()
    {
        $mock = m::mock(Node::class);

        $this->assertSame(
            $this->fixture,
            $this->fixture->__set('myNode', $mock)
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__get
     */
    public function test__get()
    {
        $mock = m::mock(Node::class);

        $this->fixture->myNode = $mock;
        $this->assertSame(
            $mock,
            $this->fixture->myNode
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::link
     */
    public function testLink()
    {
        $mock = m::mock(Edge::class);

        $this->assertSame(
            $this->fixture,
            $this->fixture->link($mock)
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::export
     */
    public function testExportException()
    {
        $graph = Graph::create('My First Graph');
        $filename = tempnam(sys_get_temp_dir(), 'tst');

        $this->expectException(\phpDocumentor\GraphViz\Exception::class);
        $graph->export('fpd', $filename);
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::export
     */
    public function testExport()
    {
        $graph = Graph::create('My First Graph');
        $filename = tempnam(sys_get_temp_dir(), 'tst');

        $this->assertSame(
            $graph,
            $graph->export('pdf', $filename)
        );
        $this->assertTrue(is_readable($filename));
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__toString
     */
    public function test__toString()
    {
        $graph = Graph::create('My First Graph');
        $this->assertSame(
            $this->normalizeLineEndings((string) $graph),
            $this->normalizeLineEndings(('digraph "My First Graph" {' . PHP_EOL . PHP_EOL . '}'))
        );

        $graph->setLabel('PigeonPost');
        $this->assertSame(
            $this->normalizeLineEndings((string) $graph),
            $this->normalizeLineEndings(('digraph "My First Graph" {' . PHP_EOL . 'label="PigeonPost"' . PHP_EOL . '}'))
        );

        $graph->setStrict(true);
        $this->assertSame(
            $this->normalizeLineEndings((string) $graph),
            $this->normalizeLineEndings(('strict digraph "My First Graph" {' . PHP_EOL . 'label="PigeonPost"' . PHP_EOL . '}'))
        );
    }

    /**
     * Help avoid issue of "#Warning: Strings contain different line endings!" on Windows.
     * @param string $string
     * @return string
     */
    private function normalizeLineEndings($string)
    {
        return preg_replace('~\R~u', "\r\n", $string);
    }
}
