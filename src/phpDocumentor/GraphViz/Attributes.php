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

namespace phpDocumentor\GraphViz;

use function array_key_exists;

trait Attributes
{
    /** @var Attribute[] */
    protected array $attributes = [];

    public function setAttribute(string $name, string|int|float|\Stringable $value): self
    {
        $this->attributes[$name] = new Attribute($name, (string) $value);

        return $this;
    }

    /**
     * @throws AttributeNotFound
     */
    public function getAttribute(string $name): Attribute
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new AttributeNotFound($name);
        }

        return $this->attributes[$name];
    }

    /**
     * Magic method to provide a getter/setter to add attributes on the Node.
     *
     * Using this method we make sure that we support any attribute without
     * too much hassle. If the name for this method does not start with get or
     * set we return null.
     *
     * Set methods return this graph (fluent interface) whilst get methods
     * return the attribute value.
     *
     * @param string  $name      Method name; either getX or setX is expected.
     * @param mixed[] $arguments List of arguments; only 1 is expected for setX.
     *
     * @throws AttributeNotFound
     *
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function __call(string $name, array $arguments): mixed
    {
        $key = $this->normalizeKey(substr($name, 3));
        $action = strtolower(substr($name, 0, 3));
        if ($action === 'set') {
            return $this->setAttribute($key, (string) $arguments[0]);
        }

        if ($action === 'get') {
            return $this->getAttribute($key);
        }

        return null;
    }

    private function normalizeKey(string $key): string
    {
        return lcfirst($key);
    }
}
