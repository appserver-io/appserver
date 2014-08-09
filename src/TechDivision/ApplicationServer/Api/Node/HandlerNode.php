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
class HandlerNode extends AbstractNode
{
    /**
     * A params node trait.
     *
     * @var \TraitInterface
     */
    use ParamsNodeTrait;

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
     * Initializes the provisioner node with the necessary data.
     *
     * @param string                                                 $type      The provisioner type
     * @param \TechDivision\ApplicationServer\Api\Node\FormatterNode $formatter The formatter node
     * @param array                                                  $params    The handler params
     */
    public function __construct($type = '', FormatterNode $formatter = null, array $params = array())
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->type = $type;
        $this->formatter = $formatter;
        $this->params = $params;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \TechDivision\ApplicationServer\Api\Node\AbstractNode::getPrimaryKey()
     */
    public function getPrimaryKey()
    {
        return $this->getType();
    }

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
