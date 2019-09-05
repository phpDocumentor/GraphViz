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

use phpDocumentor\GraphViz\Attribute;
use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Type\ObjectType;

final class AttributeGetterMethodReflection implements MethodReflection
{
    /** @var ClassReflection */
    private $classReflection;

    /** @var string */
    private $name;

    public function __construct(ClassReflection $classReflection, string $name)
    {
        $this->classReflection = $classReflection;
        $this->name = $name;
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
            [],
            false,
            new ObjectType(Attribute::class)
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
