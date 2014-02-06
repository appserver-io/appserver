<?php
/**
 * TechDivision\ApplicationServer\Api\Node\HandlerNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer handler information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class HandlerNode extends AbstractParamsNode
{

    /**
     * The handler's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The handler's formatter configuration.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\FormatterNode
     * @AS\Mapping(nodeName="formatter", nodeType="TechDivision\ApplicationServer\Api\Node\FormatterNode")
     */
    protected $formatter;

    /**
     * Returns information about the handler's class name.
     *
     * @return string The handler's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the formatter configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\FormatterNode The formatter configuration node
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}
