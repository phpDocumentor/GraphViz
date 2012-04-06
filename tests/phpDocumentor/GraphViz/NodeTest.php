<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @package   phpDocumentor\GraphViz\Tests
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpDocumentor-project.org
 */

namespace phpDocumentor\GraphViz;

require_once __DIR__ . '/../../src/GraphViz/Node.php';

/**
 * Test for the the class representing a GraphViz node.
 *
 * @package phpDocumentor\GraphViz\Tests
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpDocumentor-project.org
 */
class phpDocumentor_GraphViz_NodeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \phpDocumentor\GraphViz\Node */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new Node('name', 'label');
    }

    public function testCreate()
    {
        $this->markTestIncomplete('create test for GraphViz node must be written');
    }

    public function testName()
    {
        $this->markTestIncomplete('Name test for GraphViz node must be written');
    }

    public function testCall()
    {
        $this->markTestIncomplete('__call test for GraphViz node must be written');
    }

    public function testToString()
    {
        $this->markTestIncomplete('__toString test for GraphViz node must be written');
    }

}