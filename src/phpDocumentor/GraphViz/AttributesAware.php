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

namespace phpDocumentor\GraphViz;

trait AttributesAware
{
    /** @var Attribute[] */
    protected $attributes = [];

    /**
     * @param string $name
     *
     * @throws AttributeNotFound
     */
    public function getAttribute(string $name): Attribute
    {
        if (!\array_key_exists($name, $this->attributes)) {
            throw new AttributeNotFound($name);
        }

        return $this->attributes[$name];
    }

    public function setAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = new Attribute($name, $value);

        return $this;
    }
}
