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
     * The node that specifies the file handler to be used for this location.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\FileHandlerNode 
     * @AS\Mapping(nodeName="datasource", nodeType="TechDivision\ApplicationServer\Api\Node\FileHandlerNode")
     */
    protected $fileHandler;

    /**
     * Returns the condition to match for
     *
     * @return string The condition to match for
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Returns the node that specifies the file handler to be used for this location.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\FileHandlerNode The node that specifies the file handler to be used for this location
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }
}
