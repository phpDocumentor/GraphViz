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

namespace phpDocumentor\GraphViz\Test;

use InvalidArgumentException;
use Mockery as m;
use phpDocumentor\GraphViz\AttributeNotFound;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Exception;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function is_readable;
use function preg_replace;
use function sys_get_temp_dir;
use function tempnam;
use const PHP_EOL;

/**
 * Test for the the class representing a GraphViz graph.
 */
class GraphTest extends TestCase
{
    /** @var Graph */
    protected $fixture;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        $this->fixture = new Graph();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::create
     */
    public function testCreate() : void
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
    public function testSetName() : void
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
    public function testGetName() : void
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
    public function testSetType() : void
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
    public function testSetTypeException() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->fixture->setType('fakegraphg');
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getType
     */
    public function testGetType() : void
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

    public function testSetStrict() : void
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

    public function testIsStrict() : void
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

    public function testSetPath() : void
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
    public function test__call() : void
    {
        $this->assertNull($this->fixture->MyMethod());
        $this->assertSame($this->fixture, $this->fixture->setBgColor('black'));
        $this->assertSame('black', $this->fixture->getBgColor()->getValue());
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getAttribute
     * @covers \phpDocumentor\GraphViz\AttributeNotFound::__construct
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound() : void
    {
        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "notexisting" was not found');

        $this->fixture->getNotExisting();
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::addGraph
     */
    public function testAddGraph() : void
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
    public function testHasGraph() : void
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
    public function testGetGraph() : void
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
    public function testSetNode() : void
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
    public function testFindNode() : void
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
        $mock2    = m::mock(Node::class);
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
    public function test__set() : void
    {
        $mock = m::mock(Node::class);

        $this->fixture->__set('myNode', $mock);

        self::assertSame($mock, $this->fixture->myNode);
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__get
     */
    public function test__get() : void
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
    public function testLink() : void
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
    public function testExportException() : void
    {
        $graph    = Graph::create('My First Graph');
        $filename = tempnam(sys_get_temp_dir(), 'tst');

        if ($filename === false) {
            $this->assertFalse('Failed to create destination file');

            return;
        }

        $this->expectException(Exception::class);
        $graph->export('fpd', $filename);
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::export
     */
    public function testExport() : void
    {
        $graph    = Graph::create('My First Graph');
        $filename = tempnam(sys_get_temp_dir(), 'tst');

        if ($filename === false) {
            $this->assertFalse('Failed to create destination file');

            return;
        }

        $this->assertSame(
            $graph,
            $graph->export('pdf', $filename)
        );
        $this->assertTrue(is_readable($filename));
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__toString
     */
    public function test__toString() : void
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
            $this->normalizeLineEndings(
                ('strict digraph "My First Graph" {' . PHP_EOL . 'label="PigeonPost"' . PHP_EOL . '}')
            )
        );
    }

    /**
     * Help avoid issue of "#Warning: Strings contain different line endings!" on Windows.
     */
    private function normalizeLineEndings(string $string) : string
    {
        $result = preg_replace('~\R~u', "\r\n", $string);
        if ($result === null) {
            throw new RuntimeException('Normalize line endings failed');
        }

        return $result;
    }
}
