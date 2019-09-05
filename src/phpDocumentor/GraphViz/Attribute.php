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

use phpDocumentor\GraphViz\Contract\GraphAwareInterface;

/**
 * Class representing a single GraphViz attribute.
 *
 * @see      http://phpdoc.org
 */
class Attribute implements GraphAwareInterface
{
    use GraphAware;

    /** @var string The name of this attribute */
    protected $key = '';

    /** @var string The value of this attribute */
    protected $value = '';

    /**
     * Creating a new attribute.
     *
     * @param string $key   Id for the new attribute.
     * @param string $value Value for this attribute,
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Returns the attribute definition as is requested by GraphViz.
     */
    public function __toString(): string
    {
        $key = $this->getKey();

        if ('url' === $key) {
            $key = 'URL';
        }

        $value = $this->getValue();

        if ($this->isValueContainingSpecials()) {
            $value = '"' . $this->encodeSpecials() . '"';
        } elseif (!$this->isValueInHtml()) {
            $value = '"' . \addslashes($value) . '"';
        }

        return $key . '=' . $value;
    }

    /**
     * Returns the name for this attribute.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns the value for this attribute.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Checks whether the value contains any any special characters needing escaping.
     */
    public function isValueContainingSpecials(): bool
    {
        return false !== \mb_strpos($this->getValue(), '\\');
    }

    /**
     * Returns whether the value contains HTML.
     */
    public function isValueInHtml(): bool
    {
        $value = $this->getValue();

        return isset($value[0]) && ('<' === $value[0]);
    }

    /**
     * Sets the key for this attribute.
     *
     * @param string $key The new name of this attribute.
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Sets the value for this attribute.
     *
     * @param string $value The new value.
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Encode special characters so the escape sequences aren't removed.
     *
     * @see http://www.graphviz.org/doc/info/attrs.html#k:escString
     */
    protected function encodeSpecials(): string
    {
        $value = $this->getValue();
        $regex = '(\'|"|\\x00|\\\\(?![\\\\NGETHLnlr]))';

        return (string) \preg_replace($regex, '\\\\$0', $value);
    }
}
