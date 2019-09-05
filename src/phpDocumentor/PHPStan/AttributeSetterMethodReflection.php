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

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Reflection\Php\DummyParameter;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final class AttributeSetterMethodReflection implements MethodReflection
{
    /** @var Type */
    private $attributeType;

    /** @var ClassReflection */
    private $classReflection;

    /** @var string */
    private $name;

    public function __construct(ClassReflection $classReflection, string $name, Type $attributeType)
    {
        $this->classReflection = $classReflection;
        $this->name = $name;
        $this->attributeType = $attributeType;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->classReflection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this;
    }

    /**
     * @return ParametersAcceptor[]
     */
    public function getVariants(): array
    {
        return [new FunctionVariant(
            [new DummyParameter('value', $this->attributeType, false)],
            false,
            new ObjectType($this->classReflection->getName())
        ),
        ];
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function isStatic(): bool
    {
        return false;
    }
}
