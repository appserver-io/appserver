<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\VhostNode
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
 * DTO to transfer a vhost.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class VhostNode extends AbstractNode
{

    /**
     * The vhost's name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The vhost's application base directory.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $appBase;

    /**
     * The vhost aliases configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="aliases/alias", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\AliasNode")
     */
    protected $aliases = array();

    /**
     * Returns the vhost's name.
     *
     * @return string The vhost's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the vhost's application base directory.
     *
     * @return string The vhost's application base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * Returns the vhost's aliases configuration.
     *
     * @return array The aliases configuration
     */
    public function getAliases()
    {
        return $this->aliases;
    }
}
