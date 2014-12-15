<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ProvisionerNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer the provisioner information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ProvisionerNode extends AbstractNode implements ProvisionerNodeInterface
{

    /**
     * The provisioner name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The provisioner type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Initializes the provisioner node with the necessary data.
     *
     * @param string $name The provisioner name
     * @param string $type The provisioner type
     */
    public function __construct($name = '', $type = '')
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \AppserverIo\Appserver\Core\Api\Node\AbstractNode::getPrimaryKey()
     */
    public function getPrimaryKey()
    {
        return $this->getName();
    }

    /**
     * Returns the provisioner type.
     *
     * @return string The provisioner type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the provisioner name.
     *
     * @return string The provisioner name
     */
    public function getName()
    {
        return $this->name;
    }
}
