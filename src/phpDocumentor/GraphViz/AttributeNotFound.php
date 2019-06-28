<?php
declare(strict_types=1);

namespace phpDocumentor\GraphViz;

class AttributeNotFound extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Attribute with name "%s" was not found.', $name));
    }
}
