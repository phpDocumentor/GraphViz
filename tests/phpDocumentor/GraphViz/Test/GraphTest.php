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

use InvalidArgumentException;
use Mockery as m;
use const PHP_EOL;
use phpDocumentor\GraphViz\AttributeNotFound;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Exception;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Test for the the class representing a GraphViz graph.
 *
 * @internal
 * @coversNothing
 */
final class GraphTest extends TestCase
{
    /** @var Graph */
    protected $fixture;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->fixture = new Graph();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::addGraph
     */
    public function testAddGraph(): void
    {
        $mock = m::mock(Graph::class);
        $mock->expects('setType');
        $mock->expects('getName');

        self::assertSame(
            $this->fixture,
            $this->fixture->addGraph($mock)
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__call
     * @covers \phpDocumentor\GraphViz\Graph::getAttribute
     * @covers \phpDocumentor\GraphViz\Graph::setAttribute
     */
    public function testCall(): void
    {
        self::assertNull($this->fixture->MyMethod());
        self::assertSame($this->fixture, $this->fixture->setBgColor('black'));
        self::assertSame('black', $this->fixture->getBgColor()->getValue());
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::create
     */
    public function testCreate(): void
    {
        $fixture = Graph::create();
        self::assertInstanceOf(
            Graph::class,
            $fixture
        );
        self::assertSame(
            'G',
            $fixture->getName()
        );
        self::assertSame(
            'digraph',
            $fixture->getType()
        );

        $fixture = Graph::create('MyName', false);
        self::assertSame(
            'MyName',
            $fixture->getName()
        );
        self::assertSame(
            'graph',
            $fixture->getType()
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::export
     */
    public function testExport(): void
    {
        $graph = Graph::create('My First Graph');
        $filename = \tempnam(\sys_get_temp_dir(), 'tst');

        if (false === $filename) {
            self::assertFalse('Failed to create destination file');

            return;
        }

        self::assertSame(
            $graph,
            $graph->export('pdf', $filename)
        );
        self::assertTrue(\is_readable($filename));
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::export
     */
    public function testExportException(): void
    {
        $graph = Graph::create('My First Graph');
        $filename = \tempnam(\sys_get_temp_dir(), 'tst');

        if (false === $filename) {
            self::assertFalse('Failed to create destination file');

            return;
        }

        $this->expectException(Exception::class);
        $graph->export('fpd', $filename);
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::findNode
     */
    public function testFindNode(): void
    {
        self::assertNull($this->fixture->findNode('MyNode'));

        $mock = m::mock(Node::class);
        $mock->expects('setGraphRoot');
        $mock->expects('getName')->andReturn('MyName');

        $this->fixture->setNode($mock);
        self::assertSame(
            $mock,
            $this->fixture->findNode('MyName')
        );

        $subGraph = Graph::create();
        $mock2 = m::mock(Node::class);
        $mock2->expects('setGraphRoot');
        $mock2->expects('getName')->andReturn('MyName2');

        $subGraph->setNode($mock2);

        $this->fixture->addGraph($subGraph);
        self::assertSame(
            $mock2,
            $this->fixture->findNode('MyName2')
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__get
     */
    public function testGet(): void
    {
        $mock = m::mock(Node::class);

        $this->fixture->myNode = $mock;
        self::assertSame(
            $mock,
            $this->fixture->myNode
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getGraph
     */
    public function testGetGraph(): void
    {
        $mock = m::mock(Graph::class);
        $mock->expects('setType');
        $mock->expects('getName')->andReturn('MyName');

        $this->fixture->addGraph($mock);
        self::assertSame(
            $mock,
            $this->fixture->getGraph('MyName')
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getName
     */
    public function testGetName(): void
    {
        self::assertSame(
            $this->fixture->getName(),
            'G',
            'Expecting the name to match the initial state'
        );
        $this->fixture->setName('otherName');
        self::assertSame(
            $this->fixture->getName(),
            'otherName',
            'Expecting the name to contain the new value'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\AttributeNotFound::__construct
     * @covers \phpDocumentor\GraphViz\Graph::getAttribute
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound(): void
    {
        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "notexisting" was not found');

        $this->fixture->getNotExisting();
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::getType
     */
    public function testGetType(): void
    {
        self::assertSame(
            $this->fixture->getType(),
            'digraph'
        );
        $this->fixture->setType('graph');
        self::assertSame(
            $this->fixture->getType(),
            'graph'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::hasGraph
     */
    public function testHasGraph(): void
    {
        $mock = m::mock(Graph::class);
        $mock->expects('getName')->andReturn('MyName');
        $mock->expects('setType');

        self::assertFalse($this->fixture->hasGraph('MyName'));
        $this->fixture->addGraph($mock);
        self::assertTrue($this->fixture->hasGraph('MyName'));
    }

    public function testIsStrict(): void
    {
        self::assertSame(
            $this->fixture->isStrict(),
            false
        );
        $this->fixture->setStrict(true);
        self::assertSame(
            $this->fixture->isStrict(),
            true
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::link
     */
    public function testLink(): void
    {
        $mock = m::mock(Edge::class);
        $mock->expects('setGraphRoot');

        self::assertSame(
            $this->fixture,
            $this->fixture->link($mock)
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__set
     */
    public function testSet(): void
    {
        $mock = m::mock(Node::class);

        self::assertSame(
            $this->fixture,
            $this->fixture->__set('myNode', $mock)
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::setName
     */
    public function testSetName(): void
    {
        self::assertSame(
            $this->fixture,
            $this->fixture->setName('otherName'),
            'Expecting a fluent interface'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::setNode
     */
    public function testSetNode(): void
    {
        $mock = m::mock(Node::class);
        $mock->expects('setGraphRoot');
        $mock->expects('getName')->andReturn('MyName');

        self::assertSame(
            $this->fixture,
            $this->fixture->setNode($mock)
        );
    }

    public function testSetPath(): void
    {
        self::assertSame(
            $this->fixture,
            $this->fixture->setPath(__DIR__),
            'Expecting a fluent interface'
        );
    }

    public function testSetStrict(): void
    {
        self::assertSame(
            $this->fixture,
            $this->fixture->setStrict(true),
            'Expecting a fluent interface'
        );
        self::assertSame(
            $this->fixture,
            $this->fixture->setStrict(false),
            'Expecting a fluent interface'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::setType
     */
    public function testSetType(): void
    {
        self::assertSame(
            $this->fixture,
            $this->fixture->setType('digraph'),
            'Expecting a fluent interface'
        );
        self::assertSame(
            $this->fixture,
            $this->fixture->setType('graph'),
            'Expecting a fluent interface'
        );
        self::assertSame(
            $this->fixture,
            $this->fixture->setType('subgraph'),
            'Expecting a fluent interface'
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::setType
     */
    public function testSetTypeException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->fixture->setType('fakegraphg');
    }

    /**
     * @covers \phpDocumentor\GraphViz\Graph::__toString
     */
    public function testToString(): void
    {
        $graph = Graph::create('My First Graph');
        self::assertSame(
            $this->normalizeLineEndings((string) $graph),
            $this->normalizeLineEndings(('digraph "My First Graph" {' . PHP_EOL . PHP_EOL . '}'))
        );

        $graph->setLabel('PigeonPost');
        self::assertSame(
            $this->normalizeLineEndings((string) $graph),
            $this->normalizeLineEndings(('digraph "My First Graph" {' . PHP_EOL . 'label="PigeonPost"' . PHP_EOL . '}'))
        );

        $graph->setStrict(true);
        self::assertSame(
            $this->normalizeLineEndings((string) $graph),
            $this->normalizeLineEndings(
                ('strict digraph "My First Graph" {' . PHP_EOL . 'label="PigeonPost"' . PHP_EOL . '}')
            )
        );
    }

    /**
     * Help avoid issue of "#Warning: Strings contain different line endings!" on Windows.
     *
     * @param string $string
     */
    private function normalizeLineEndings(string $string): string
    {
        $result = \preg_replace('~\R~u', "\r\n", $string);

        if (null === $result) {
            throw new RuntimeException('Normalize line endings failed');
        }

        return $result;
    }
}
