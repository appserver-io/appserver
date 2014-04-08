<?php
/**
 * TechDivision\ApplicationServer\Api\Node\LoggerNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer logger information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class LoggerNode extends AbstractNode
{

    /**
     * The system logger's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * the logger's name
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The system logger's channel name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $channelName;

    /**
     * Array with nodes for the registered processors.
     *
     * @var array
     * @AS\Mapping(nodeName="processors/processor", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ProcessorNode")
     */
    protected $processors = array();

    /**
     * Array with nodes for the registered handlers.
     *
     * @var array
     * @AS\Mapping(nodeName="handlers/handler", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\HandlerNode")
     */
    protected $handlers = array();

    /**
     * Returns information about the system logger's class name.
     *
     * @return string The system logger's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns logger's name
     *
     * @return string The logger's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns information about the system logger's channel name.
     *
     * @return string The system logger's channel name
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * Returns the array with all registered processors.
     *
     * @return array The registered processors
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Returns the array with all registered handlers.
     *
     * @return array The registered handlers
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
