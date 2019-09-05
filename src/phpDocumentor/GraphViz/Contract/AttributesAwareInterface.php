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
    public function getAttribute(string $name): Attribute;

    public function setAttribute(string $name, string $value);
}
