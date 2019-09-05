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

trait GraphAware
{
    /** @var null|\phpDocumentor\GraphViz\Graph */
    protected $graph;

    public function getGraphRoot(): ?Graph
    {
        return $this->graph;
    }

    public function setGraphRoot(Graph $graph): void
    {
        $this->graph = $graph;
    }
}
