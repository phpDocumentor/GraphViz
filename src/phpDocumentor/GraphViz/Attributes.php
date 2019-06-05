<?php
declare(strict_types=1);

namespace phpDocumentor\GraphViz;

trait Attributes
{
    /** @var Attribute[] */
    protected $attributes = [];

    public function setAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = new Attribute($name, $value);

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
}
