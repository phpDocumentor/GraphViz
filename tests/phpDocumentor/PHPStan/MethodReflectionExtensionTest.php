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

namespace phpDocumentor\GraphViz\PHPStan;

use Mockery as m;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\FloatType;
use PHPStan\Type\StringType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class MethodReflectionExtensionTest extends TestCase
{
    /** @var MethodReflectionExtension */
    private $fixture;

    protected function setUp(): void
    {
        $this->fixture = new MethodReflectionExtension();
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function existingMethodProvider(): array
    {
        return [
            'node::getLabel' => [
                'className' => Node::class,
                'methodName' => 'getLabel',
            ],
            'node::setLabel' => [
                'className' => Node::class,
                'methodName' => 'setLabel',
            ],
            'graph::setFontSize' => [
                'className' => Graph::class,
                'methodName' => 'setFontSize',
            ],
            'graph::getFontSize' => [
                'className' => Graph::class,
                'methodName' => 'getFontSize',
            ],
        ];
    }

    public function testAttributeType(): void
    {
        $classReflection = m::mock(ClassReflection::class);
        $classReflection->shouldReceive('getName')->andReturn(Node::class);

        $method = $this->fixture->getMethod($classReflection, 'setFontSize');

        self::assertInstanceOf(FloatType::class, $method->getVariants()[0]->getParameters()[0]->getType());
    }

    public function testAttributeTypeOfNoneExisting(): void
    {
        $classReflection = m::mock(ClassReflection::class);
        $classReflection->shouldReceive('getName')->andReturn(Node::class);

        $method = $this->fixture->getMethod($classReflection, 'setColor');

        self::assertInstanceOf(StringType::class, $method->getVariants()[0]->getParameters()[0]->getType());
    }

    /**
     * @dataProvider existingMethodProvider
     *
     * @param string $className
     * @param string $methodName
     */
    public function testNodeHasMethodReturnsTrue(string $className, string $methodName): void
    {
        $classReflection = m::mock(ClassReflection::class);
        $classReflection->shouldReceive('getName')->andReturn($className);

        self::assertTrue($this->fixture->hasMethod($classReflection, $methodName));
    }
}
