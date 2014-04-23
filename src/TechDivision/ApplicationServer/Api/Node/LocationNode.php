<?php
/**
 * TechDivision\ApplicationServer\Api\Node\LocationNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer location information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class LocationNode extends AbstractNode
{

    /**
     * The condition to match for.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $condition;

    /**
     * The file handlers
     *
     * @var array
     * @AS\Mapping(nodeName="fileHandlers/fileHandler", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\FileHandlerNode")
     */
    protected $fileHandlers;

    /**
     * Returns the condition to match for.
     *
     * @return string The condition to match for
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Returns the file handler nodes.
     *
     * @return array The file handler nodes
     */
    public function getFileHandlers()
    {
        return $this->fileHandlers;
    }
}
