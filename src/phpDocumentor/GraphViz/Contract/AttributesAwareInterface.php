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

namespace phpDocumentor\GraphViz\Contract;

use phpDocumentor\GraphViz\Attribute;

interface AttributesAwareInterface
{
    /**
     * @param string $name
     *
     * @return \phpDocumentor\GraphViz\Attribute
     */
    public function getAttribute(string $name): Attribute;

    /**
     * @param string $name
     * @param string $value
     *
     * @return mixed
     */
    public function setAttribute(string $name, string $value);
}
