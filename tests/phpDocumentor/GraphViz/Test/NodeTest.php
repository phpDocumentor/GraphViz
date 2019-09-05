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

use phpDocumentor\GraphViz\AttributeNotFound;
use phpDocumentor\GraphViz\Node;
use PHPUnit\Framework\TestCase;

/**
 * Test for the the class representing a GraphViz node.
 *
 * @internal
 * @coversNothing
 */
final class NodeTest extends TestCase
{
    /** @var Node */
    protected $fixture;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp(): void
    {
        $this->fixture = new Node('name', 'label');
    }

    /**
     * Tests the magic __call method, to work as described, return the object
     * instance for a setX method, return the value for an getX method, and null
     * for the remaining method calls.
     *
     * @covers \phpDocumentor\GraphViz\Node::__call
     * @covers \phpDocumentor\GraphViz\Node::getAttribute
     * @covers \phpDocumentor\GraphViz\Node::setAttribute
     */
    public function testCall(): void
    {
        $fontname = 'Bitstream Vera Sans';
        self::assertInstanceOf(Node::class, $this->fixture->setfontname($fontname));
        self::assertSame($fontname, $this->fixture->getfontname()->getValue());
        self::assertNull($this->fixture->someNonExistingMethod());
    }

    /**
     * Tests the construct method.
     *
     * @covers \phpDocumentor\GraphViz\Node::__construct
     * @returnn void
     */
    public function testConstruct(): void
    {
        $fixture = new Node('MyName', 'MyLabel');
        self::assertInstanceOf(
            Node::class,
            $fixture
        );
        self::assertSame('MyName', $fixture->getName());
        self::assertSame('MyLabel', $fixture->getLabel()->getValue());
    }

    /**
     * Tests the create method.
     *
     * @covers \phpDocumentor\GraphViz\Node::create
     * @returnn void
     */
    public function testCreate(): void
    {
        self::assertInstanceOf(
            Node::class,
            Node::create('name', 'label')
        );
    }

    /**
     * @covers \phpDocumentor\GraphViz\AttributeNotFound::__construct
     * @covers \phpDocumentor\GraphViz\Node::getAttribute
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound(): void
    {
        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "fontname" was not found');

        $this->fixture->getFontname();
    }

    /**
     * Tests the getting and setting of the name.
     *
     * @covers \phpDocumentor\GraphViz\Node::getName
     * @covers \phpDocumentor\GraphViz\Node::setName
     */
    public function testName(): void
    {
        self::assertSame(
            $this->fixture->getName(),
            'name',
            'Expecting the name to match the initial state'
        );
        self::assertSame(
            $this->fixture,
            $this->fixture->setName('otherName'),
            'Expecting a fluent interface'
        );
        self::assertSame(
            $this->fixture->getName(),
            'otherName',
            'Expecting the name to contain the new value'
        );
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard.
     *
     * @covers \phpDocumentor\GraphViz\Node::__toString
     */
    public function testToString(): void
    {
        $this->fixture->setfontsize(12);
        $this->fixture->setfontname('Bitstream Vera Sans');

        $dot = <<<'DOT'
"name" [
label="label"
fontsize="12"
fontname="Bitstream Vera Sans"
]
DOT;

        self::assertSame($dot, (string) $this->fixture);
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard when the label contains slashes.
     *
     * @covers \phpDocumentor\GraphViz\Node::__toString
     */
    public function testToStringWithLabelContainingSlashes(): void
    {
        $this->fixture->setfontsize(12);
        $this->fixture->setfontname('Bitstream Vera Sans');
        $this->fixture->setLabel('\phpDocumentor\Descriptor\ProjectDescriptor');

        $dot = <<<'DOT'
"name" [
label="\\phpDocumentor\\Descriptor\\ProjectDescriptor"
fontsize="12"
fontname="Bitstream Vera Sans"
]
DOT;

        self::assertSame($dot, (string) $this->fixture);
    }
}
