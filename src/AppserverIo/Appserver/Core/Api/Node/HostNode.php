<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\HostNode
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
 * DTO to transfer a host.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class HostNode extends AbstractNode
{

    // We use several traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
    use ContextsNodeTrait;

    /**
     * The host name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The applications base directory.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $appBase;

    /**
     * The server admin's mail address.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $serverAdmin;

    /**
     * The servers software signature.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $serverSoftware;

    /**
     * Returns the host name.
     *
     * @return string The host name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the applications base directory.
     *
     * @return string The applications base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * Returns the server admin's mail address.
     *
     * @return string The server admin's mail address
     */
    public function getServerAdmin()
    {
        return $this->serverAdmin;
    }

    /**
     * Returns the server's software signature.
     *
     * @return string The server's software signature
     */
    public function getServerSoftware()
    {
        return $this->serverSoftware;
    }
}
