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

use InvalidArgumentException;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use RuntimeException;
use SimpleXMLElement;

final class MethodReflectionExtension implements MethodsClassReflectionExtension
{
    private const SUPPORTED_CLASSES = [
        Node::class => 'node',
        Graph::class => 'graph',
        Edge::class => 'edge',
    ];

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        if (0 === \mb_stripos($methodName, 'get')) {
            return new AttributeGetterMethodReflection($classReflection, $methodName);
        }

        $attributeName = $this->getAttributeFromMethodName($methodName);

        return new AttributeSetterMethodReflection(
            $classReflection,
            $methodName,
            $this->getAttributeInputType($attributeName)
        );
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (!\array_key_exists($classReflection->getName(), static::SUPPORTED_CLASSES)) {
            return false;
        }

        $methods = $this->getMethodsFromSpec(static::SUPPORTED_CLASSES[$classReflection->getName()]);
        $expectedAttribute = $this->getAttributeFromMethodName($methodName);

        return \in_array($expectedAttribute, $methods, true);
    }

    private function getAttributeFromMethodName(string $methodName): string
    {
        return \mb_strtolower(\mb_substr($methodName, 3));
    }

    private function getAttributeInputType(string $ref): Type
    {
        $simpleXml = $this->getAttributesXmlDoc();
        $attributes = $simpleXml->xpath(\sprintf("xsd:attribute[@name='%s']", $ref));

        if (empty($attributes)) {
            return new StringType();
        }

        $type = $attributes[0]['type'];
        $type = \str_replace('xsd:', '', $type);

        switch ($type) {
            case 'boolean':
                return new BooleanType();
            case 'decimal':
                return new FloatType();
            case 'string':
            default:
                return new StringType();
        }
    }

    private function getAttributesXmlDoc(): SimpleXMLElement
    {
        $fileContent = \file_get_contents(__DIR__ . '/assets/attributes.xml');

        if (false === $fileContent) {
            throw new RuntimeException('Cannot read attributes spec');
        }

        $xml = \simplexml_load_string($fileContent);

        if (false === $xml) {
            throw new RuntimeException('Cannot read attributes spec');
        }

        return $xml;
    }

    /**
     * @param string $className
     *
     * @return string[]
     */
    private function getMethodsFromSpec(string $className): array
    {
        $simpleXml = $this->getAttributesXmlDoc();

        $elements = $simpleXml->xpath(\sprintf("xsd:complexType[@name='%s']/xsd:attribute", $className));

        if (false === $elements) {
            throw new InvalidArgumentException(
                \sprintf('Class "%s" does not exist in Graphviz spec', $className)
            );
        }

        return \array_map(
            static function (SimpleXMLElement $attribute): string {
                return \mb_strtolower((string) $attribute['ref']);
            },
            $elements
        );
    }
}
