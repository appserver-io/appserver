<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\ServerNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer server information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ServerNode extends AbstractNode
{
    // We use several traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
    use EnvironmentVariablesNodeTrait;
    use ParamsNodeTrait;
    use RewriteMapsNodeTrait;
    use RewritesNodeTrait;
    use AccessesNodeTrait;
    use LocationsNodeTrait;
    use AuthenticationsNodeTrait;

    /**
     * The servers type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The servers name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The worker to use.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $worker;

    /**
     * The socket to use.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $socket;

    /**
     * The loggers name to use.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $loggerName;

    /**
     * The server context to use.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $serverContext;

    /**
     * The request context to use.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $requestContext;

    /**
     * The virtual hosts.
     *
     * @var array
     * @AS\Mapping(nodeName="virtualHosts/virtualHost", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\VirtualHostNode")
     */
    protected $virtualHosts;

    /**
     * The connection handlers.
     *
     * @var array
     * @AS\Mapping(nodeName="connectionHandlers/connectionHandler", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ConnectionHandlerNode")
     */
    protected $connectionHandlers;

    /**
     * The modules.
     *
     * @var array
     * @AS\Mapping(nodeName="modules/module", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ModuleNode")
     */
    protected $modules;

    /**
     * The file handlers.
     *
     * @var array
     * @AS\Mapping(nodeName="fileHandlers/fileHandler", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\FileHandlerNode")
     */
    protected $fileHandlers;

    /**
     * Returns the servers type.
     *
     * @return string The servers type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the server name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the worker to use for server.
     *
     * @return string The worker type to use for server
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * Returns the socket to use.
     *
     * @return string The socket type
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Returns the loggers name to use.
     *
     * @return string The loggers name
     */
    public function getLoggerName()
    {
        return $this->loggerName;
    }

    /**
     * Returns the server context to use.
     *
     * @return string The server context type
     */
    public function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * Returns the request context to use.
     *
     * @return string The request context type
     */
    public function getRequestContext()
    {
        return $this->requestContext;
    }

    /**
     * Returns the virtual hosts.
     *
     * @return array
     */
    public function getVirtualHosts()
    {
        return $this->virtualHosts;
    }

    /**
     * Returns the connection handler nodes.
     *
     * @return array
     */
    public function getConnectionHandlers()
    {
        return $this->connectionHandlers;
    }

    /**
     * Returns the file handler nodes.
     *
     * @return array
     */
    public function getFileHandlers()
    {
        return $this->fileHandlers;
    }

    /**
     * Returns the module nodes.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }
}
