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

use phpDocumentor\GraphViz\Attribute;
use phpDocumentor\GraphViz\AttributeNotFound;
use phpDocumentor\GraphViz\Attributes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \phpDocumentor\GraphViz\Attributes
 */
class AttributesTest extends TestCase
{
    public function testSetAndGetAttribute(): void
    {
        $obj = new class() {
            use Attributes;
        };

        $obj->setAttribute('color', 'red');
        $attribute = $obj->getAttribute('color');

        $this->assertSame('color', $attribute->getKey());
        $this->assertSame('red', $attribute->getValue());
    }

    public function testGetAttributeThrowsExceptionIfNotFound(): void
    {
        $obj = new class() {
            use Attributes;
        };

        $this->expectException(AttributeNotFound::class);
        $obj->getAttribute('missing');
    }

    public function testMagicSet(): void
    {
        $obj = new class() {
            use Attributes;
        };

        $result = $obj->setColor('blue');

        $this->assertSame($obj, $result);
    }

    public function testMagicGet(): void
    {
        $obj = new class() {
            use Attributes;
        };
        $obj->setColor('blue');

        $attribute = $obj->getColor();

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertSame('color', $attribute->getKey());
        $this->assertSame('blue', $attribute->getValue());
    }

    public function testMagicCallReturnsNullForUnknownMethod(): void
    {
        $obj = new class() {
            use Attributes;
        };

        $this->assertNull($obj->fooBar('baz'));
    }

    public function testSetAttributeThenMagicGet(): void
    {
        $obj = new class() {
            use Attributes;
        };

        $obj->setAttribute('mainShape', 'box');
        $attribute = $obj->getMainShape();

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertSame('mainShape', $attribute->getKey());
        $this->assertSame('box', $attribute->getValue());
    }

    public function testMagicSetThenGetAttribute(): void
    {
        $obj = new class() {
            use Attributes;
        };

        $obj->setMainShape('box');
        $attribute = $obj->getAttribute('mainShape');

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertSame('mainShape', $attribute->getKey());
        $this->assertSame('box', $attribute->getValue());
    }
}
