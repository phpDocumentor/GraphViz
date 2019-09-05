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

use phpDocumentor\GraphViz\Attribute;
use PHPUnit\Framework\TestCase;

/**
 * Test for the the class representing a GraphViz attribute.
 *
 * @internal
 * @coversNothing
 */
final class AttributeTest extends TestCase
{
    /** @var Attribute */
    protected $fixture;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp(): void
    {
        $this->fixture = new Attribute('a', '1');
    }

    /**
     * Tests the construct method.
     *
     * @covers \phpDocumentor\GraphViz\Attribute::__construct
     * @returnn void
     */
    public function testConstruct(): void
    {
        $fixture = new Attribute('MyKey', 'MyValue');
        self::assertInstanceOf(
            Attribute::class,
            $fixture
        );
        self::assertSame('MyKey', $fixture->getKey());
        self::assertSame('MyValue', $fixture->getValue());
    }

    /**
     * Tests whether the isValueContainingSpecials function.
     *
     * @covers \phpDocumentor\GraphViz\Attribute::isValueContainingSpecials
     */
    public function testIsValueContainingSpecials(): void
    {
        $this->fixture->setValue('+ name : string\l+ home_country : string\l');
        self::assertTrue($this->fixture->isValueContainingSpecials());

        $this->fixture->setValue('+ ship(): boolean');
        self::assertFalse($this->fixture->isValueContainingSpecials());
    }

    /**
     * Tests whether a string starting with a < is recognized as HTML.
     *
     * @covers \phpDocumentor\GraphViz\Attribute::isValueInHtml
     */
    public function testIsValueInHtml(): void
    {
        $this->fixture->setValue('a');
        self::assertFalse(
            $this->fixture->isValueInHtml(),
            'Expected value to not be a HTML code'
        );

        $this->fixture->setValue('<a>test</a>');
        self::assertTrue(
            $this->fixture->isValueInHtml(),
            'Expected value to be recognized as a HTML code'
        );
    }

    /**
     * Tests the getting and setting of the key.
     *
     * @covers \phpDocumentor\GraphViz\Attribute::getKey
     * @covers \phpDocumentor\GraphViz\Attribute::setKey
     */
    public function testKey(): void
    {
        self::assertSame(
            $this->fixture->getKey(),
            'a',
            'Expecting the key to match the initial state'
        );
        self::assertSame(
            $this->fixture,
            $this->fixture->setKey('b'),
            'Expecting a fluent interface'
        );
        self::assertSame(
            $this->fixture->getKey(),
            'b',
            'Expecting the key to contain the new value'
        );
    }

    /**
     * Tests whether the toString provides a valid GraphViz attribute string.
     *
     * @covers \phpDocumentor\GraphViz\Attribute::__toString
     */
    public function testToString(): void
    {
        $this->fixture = new Attribute('a', 'b');
        self::assertSame(
            'a="b"',
            (string) $this->fixture,
            'Strings should be surrounded with quotes'
        );

        $this->fixture->setValue('a"a');
        self::assertSame(
            'a="a\"a"',
            (string) $this->fixture,
            'Strings should be surrounded with quotes'
        );

        $this->fixture->setKey('url');
        self::assertSame(
            'URL="a\"a"',
            (string) $this->fixture,
            'The key named URL must be uppercased'
        );

        $this->fixture->setValue('<a>test</a>');
        self::assertSame(
            'URL=<a>test</a>',
            (string) $this->fixture,
            'HTML strings should not be surrounded with quotes'
        );
    }

    /**
     * Tests whether the toString provides a valid GraphViz attribute string.
     *
     * @covers \phpDocumentor\GraphViz\Attribute::__toString
     * @covers \phpDocumentor\GraphViz\Attribute::encodeSpecials
     */
    public function testToStringWithSpecials(): void
    {
        $this->fixture = new Attribute('a', 'b');

        $this->fixture->setValue('a\la');
        self::assertSame(
            'a="a\la"',
            (string) $this->fixture,
            'Specials should not be escaped'
        );
        $this->fixture->setValue('a\l"a');
        self::assertSame(
            'a="a\l\"a"',
            (string) $this->fixture,
            'Specials should not be escaped, but quotes should'
        );
        $this->fixture->setValue('a\\\\l"a');
        self::assertSame(
            'a="a\\\\l\"a"',
            (string) $this->fixture,
            'Double backslashes should stay the same'
        );
    }

    /**
     * Tests the getting and setting of the value.
     *
     * @covers \phpDocumentor\GraphViz\Attribute::getValue
     * @covers \phpDocumentor\GraphViz\Attribute::setValue
     */
    public function testValue(): void
    {
        self::assertSame(
            $this->fixture->getValue(),
            '1',
            'Expecting the value to match the initial state'
        );
        self::assertSame(
            $this->fixture,
            $this->fixture->setValue('2'),
            'Expecting a fluent interface'
        );
        self::assertSame(
            $this->fixture->getValue(),
            '2',
            'Expecting the value to contain the new value'
        );
    }
}
