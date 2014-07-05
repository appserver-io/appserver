<?php

/**
 * TechDivision\ApplicationServer\GenericApplication
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\PBC\AutoLoader;
use TechDivision\PBC\Config;

use TechDivision\Storage\GenericStackable;
use TechDivision\Servlet\Servlet;
use TechDivision\Servlet\ServletContext;
use TechDivision\ServletEngine\Http\RequestContext;
use TechDivision\ServletEngine\VirtualHost;
use TechDivision\ServletEngine\ServletManager;
use TechDivision\MessageQueue\QueueManager;
use TechDivision\PersistenceContainer\BeanManager;
use TechDivision\WebSocketServer\HandlerManager;
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Api\ContainerService;
use TechDivision\ApplicationServer\Api\Node\AppNode;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;

/**
 * The application instance holds all information about the deployed application
 * and provides a reference to the servlet manager and the initial context.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class GenericApplication extends \Thread implements ApplicationInterface
{

    /**
     * The applications base directory.
     *
     * @var string
     */
    protected $appBase;

    /**
     * The web containers base directory.
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * The unique application name.
     *
     * @var string
     */
    protected $name;

    /**
     * The host configuration.
     *
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;

    /**
     * The initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The session manager that is bound to the request.
     *
     * @var \TechDivision\ServletEngine\SessionManager
     */
    protected $sessionManager;

    /**
     * The authentication manager that is bound to the request.
     *
     * @var \TechDivision\ServletEngine\AuthenticationManager
     */
    protected $authenticationManager;

    /**
     * The queue manager.
     *
     * @var \TechDivision\MessageQueue\QueueManager
     */
    protected $queueManager;

    /**
     * The queue manager.
     *
     * @var \TechDivision\PersistenceContainer\BeanManager
     */
    protected $beanManager;

    /**
     * The servlet context that handles the servlets of this application.
     *
     * @var \TechDivision\Servlet\ServletContext
     */
    protected $servletContext;

    /**
     * The handler manager that handles the handlers of this application.
     *
     * @var \TechDivision\WebSocketServer\HandlerManager
     */
    protected $handlerManager;

    /**
     * Storage for the available VHost configurations.
     *
     * @var \TechDivision\Storage\GenericStackable
     */
    protected $vhosts;

    /**
     * Storage for the available class loaders.
     *
     * @var \TechDivision\Storage\GenericStackable
     */
    protected $classLoaders;

    /**
     * Initializes the application context.
     */
    public function __construct()
    {
        $this->vhosts = new GenericStackable();
        $this->classLoaders = new GenericStackable();
    }

    /**
     * Returns a attribute from the application context.
     *
     * @param string $name the name of the attribute to return
     *
     * @throws \Exception
     * @return void
     */
    public function getAttribute($name)
    {
        throw new \Exception(__METHOD__ . ' not implemented yet');
    }

    /**
     * The initial context instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     *
     * @return void
     */
    public function injectInitialContext($initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Injects the application name.
     *
     * @param string $name The application name
     *
     * @return void
     */
    public function injectName($name)
    {
        $this->name = $name;
    }

    /**
     * Injects the applications base directory.
     *
     * @param string $appBase The applications base directory
     *
     * @return void
     */
    public function injectAppBase($appBase)
    {
        $this->appBase = $appBase;
    }

    /**
     * Injects the containers base directory.
     *
     * @param string $baseDirectory The web containers base directory
     *
     * @return void
     */
    public function injectBaseDirectory($baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * Injects the applications servlet context instance.
     *
     * @param \TechDivision\Servlet\ServletContext $servletContext The servlet context instance
     *
     * @return void
     */
    public function injectServletContext(ServletContext $servletContext)
    {
        $this->servletContext = $servletContext;
    }

    /**
     * Injects the applications handler manager instance.
     *
     * @param \TechDivision\WebSocketServer\HandlerManager $handlerManager The handler manager instance
     *
     * @return void
     */
    public function injectHandlerManager(HandlerManager $handlerManager)
    {
        $this->handlerManager = $handlerManager;
    }

    /**
     * Injects the session manager that is bound to the request.
     *
     * @param \TechDivision\ServletEngine\SessionManager $sessionManager The session manager to bound this request to
     *
     * @return void
     */
    public function injectSessionManager($sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Injects the authentication manager that is bound to the request.
     *
     * @param \TechDivision\ServletEngine\AuthenticationManager $authenticationManager The authentication manager to bound this request to
     *
     * @return void
     */
    public function injectAuthenticationManager($authenticationManager)
    {
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * Injects the container node the application is belonging to
     *
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode $containerNode The container node the application is belonging to
     *
     * @return void
     */
    public function injectContainerNode($containerNode)
    {
        $this->containerNode = $containerNode;
    }

    /**
     * Sets the applications queue manager instance.
     *
     * @param \TechDivision\MessageQueue\QueueManager $queueManager The queue manager instance
     *
     * @return void
     */
    public function injectQueueManager(QueueManager $queueManager)
    {
        $this->queueManager = $queueManager;
    }

    /**
     * Injects the applications bean manager instance.
     *
     * @param \TechDivision\PersistenceContainer\BeanManager $beanManager The bean manager instance
     *
     * @return void
     */
    public function injectBeanManager(BeanManager $beanManager)
    {
        $this->beanManager = $beanManager;
    }

    /**
     * Returns the session manager instance associated with this request.
     *
     * @return \TechDivision\ServletEngine\SessionManager The session manager instance
     */
    public function getSessionManager()
    {
        return $this->sessionManager;
    }

    /**
     * Returns the authentication manager instance associated with this request.
     *
     * @return \TechDivision\ServletEngine\AuthenticationManager The authentication manager instance
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * Return the queue manager instance.
     *
     * @return \TechDivision\MessageQueue\QueueManager The queue manager instance
     */
    public function getQueueManager()
    {
        return $this->queueManager;
    }

    /**
     * Returns the application name (that has to be the class namespace, e.g. TechDivision\Example)
     *
     * @return string The application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the servlet context instance.
     *
     * @return \TechDivision\Servlet\ServletContext The servlet context instance
     */
    public function getServletContext()
    {
        return $this->servletContext;
    }

    /**
     * Return the handler manager instance.
     *
     * @return \TechDivision\WebSocketServer\HandlerManager The handler manager instance
     */
    public function getHandlerManager()
    {
        return $this->handlerManager;
    }

    /**
     * Return the bean manager instance.
     *
     * @return \TechDivision\PersistenceContainer\BeanManager The bean manager instance
     */
    public function getBeanManager()
    {
        return $this->beanManager;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $directoryToAppend The directory to append to the base directory
     *
     * @return string The base directory with appended dir if given
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        $baseDirectory = $this->baseDirectory;
        if ($directoryToAppend != null) {
            $baseDirectory .= $directoryToAppend;
        }
        return $baseDirectory;
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The path to the webapps folder
     */
    public function getWebappPath()
    {
        return $this->getAppBase() . DIRECTORY_SEPARATOR . $this->getName();
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The app base
     * @see ContainerService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return ServiceInterface The service instance
     * @see InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the initial context instance.
     *
     * @return InitialContext The initial Context
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Return's the applications available VHost configurations.
     *
     * @return \ArrayAccess The available VHost configurations
     */
    public function getVhosts()
    {
        return $this->vhosts;
    }

    /**
     * Return the class loaders.
     *
     * @return \ArrayAccess The class loader instances
     */
    public function getClassLoaders()
    {
        return $this->classLoaders;
    }

    /**
     * Checks if the application is a virtual host for the passed server name.
     *
     * @param string $serverName The server name to check the application being a virtual host of
     *
     * @return boolean TRUE if the application is a virtual host, else FALSE
     */
    public function isVHostOf($serverName)
    {

        // check if the application is a virtual host for the passed server name
        foreach ($this->getVHosts() as $virtualHost) {

            // compare the virtual host name itself
            if (strcmp($virtualHost->getName(), $serverName) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Bounds the application to the passed virtual host.
     *
     * @param \TechDivision\ServletEngine\VirtualHost $virtualHost The virtual host to add
     *
     * @return void
     */
    public function addVirtualHost(VirtualHost $virtualHost)
    {
        $this->vhosts[] = $virtualHost;
    }

    /**
     * Injects an additional class loader.
     *
     * @param object $classLoader A class loader to put on the class loader stack
     *
     * @return void
     */
    public function addClassLoader($classLoader)
    {
        $this->classLoaders[] = $classLoader;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The connected application
     */
    public function connect()
    {
        // start the application instance
        $this->start();

        // return the instance itself
        return $this;
    }

    /**
     * Registers all class loaders injected to the applications in the opposite
     * order as they have been injected.
     *
     * @return void
     */
    public function registerClassLoaders()
    {

        // initialize the class loader with the additional folders
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath());
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'classes');
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'lib');
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'META-INF' . DIRECTORY_SEPARATOR . 'classes');
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getWebappPath() . DIRECTORY_SEPARATOR . 'META-INF' . DIRECTORY_SEPARATOR . 'lib');

        /**
         * @TODO Refactor to allow PBC class loader also, maybe with a class loader factory!
         *
         * $config Config::getInstance();
         * $config->setXXX();
         *
         * $classLoader = new AutoLoader($config);
         *
         * $autoLoaderConfig = $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'pbc.conf.json';
         * if (file_exists($autoLoaderConfig)) {
         *     Config::getInstance()->load($autoLoaderConfig);
         *     $classLoader = new AutoLoader();
         *     $classLoader->register();
         * }
         */

        foreach ($this->getClassLoaders() as $classLoader) {
            $classLoader->register(true, true);
        }
    }

    /**
     * This is the threads main() method that initializes the application with the autoloader and
     * instanciates all the necessary manager instances.
     *
     * @return void
     */
    public function run()
    {

        // register the class loaders
        $this->registerClassLoaders();

        // load and initialize the servlets
        if ($this->servletContext) {
            $this->servletContext->initialize();
        }

        // load and initialize the handlers
        if ($this->handlerManager) {
            $this->handlerManager->initialize();
        }

        // load and initialize the session manager
        if ($this->sessionManager) {

            // prepare the default session save path
            $sessionSavePath = $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'sessions';

            // load the settings, set the default session save path
            $sessionSettings = $this->sessionManager->getSessionSettings();
            $sessionSettings->setSessionSavePath($sessionSavePath);

            // if we've session parameters defined in our servlet context
            if ($this->servletContext && $this->servletContext->hasSessionParameters()) {

                // we want to merge the session settings from the servlet context into our session manager
                $sessionSettings->mergeServletContext($this->servletContext);
            }

            // initialize the session manager
            $this->sessionManager->initialize();
        }

        // we do nothing here
        while (true) {
            $this->wait();
        }
    }
}
