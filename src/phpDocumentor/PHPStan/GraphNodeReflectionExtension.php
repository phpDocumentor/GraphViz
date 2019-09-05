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

use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use PHPStan\Reflection\Annotations\AnnotationPropertyReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\ObjectType;

final class GraphNodeReflectionExtension implements PropertiesClassReflectionExtension
{
    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        return new AnnotationPropertyReflection(
            $classReflection,
            new ObjectType(Node::class),
            true,
            true
        );
    }

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if (Graph::class === $classReflection->getName()) {
            return true;
        }

        return false;
    }
}
