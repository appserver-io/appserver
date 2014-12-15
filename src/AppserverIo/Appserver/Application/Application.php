<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application;

use AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface;
use AppserverIo\Appserver\Naming\BindingTrait;
use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Appserver\Naming\NamingDirectory;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Naming\NamingDirectoryInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Lang\Reflection\ReflectionObject;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Psr\EnterpriseBeans\Annotations\AnnotationKeys;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\VirtualHostInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;

/**
 * AppserverIo\Appserver\Application\Application
 *
 * The application instance holds all information about the deployed application
 * and provides a reference to the servlet manager and the initial context.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 *
 * @property \AppserverIo\Storage\StorageInterface                          $data            Application's data storage
 * @property \AppserverIo\Storage\GenericStackable                          $classLoaders    Stackable holding all class loaders this application has registered
 * @property \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext  The initial context instance
 * @property \AppserverIo\Storage\GenericStackable                          $managers        Stackable of managers for this application
 * @property string                                                         $name            Name of the application
 * @property \AppserverIo\Psr\Naming\NamingDirectoryInterface               $namingDirectory The naming directory instance
 * @property \AppserverIo\Storage\GenericStackable                          $virtualHosts    Stackable containing all virtual hosts used for this application
 */
class Application extends \Thread implements ApplicationInterface
{

    /**
     * Trait which allows to bind instances and callbacks to the application
     */
    use BindingTrait;

    /**
     * The time we wait after each loop.
     *
     * @var integer
     */
    const TIME_TO_LIVE = 1;

    /**
     * Initializes the application context.
     */
    public function __construct()
    {
        $this->connected = false;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     * @see \AppserverIo\Psr\Context\Context::getAttribute()
     */
    public function getAttribute($key)
    {
        return $this->data->get($key);
    }

    /**
     * Queries if the attribute with the passed key is bound.
     *
     * @param string $key The key of the attribute to query
     *
     * @return boolean TRUE if the attribute is bound, else FALSE
     */
    public function hasAttribute($key)
    {
        return $this->data->has($key);
    }

    /**
     * Sets the passed key/value pair in the directory.
     *
     * @param string $key   The attributes key
     * @param mixed  $value Tha attribute to be bound
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->data->set($key, $value);
    }

    /**
     * Returns the keys of the bound attributes.
     *
     * @return array The keys of the bound attributes
     */
    public function getAllKeys()
    {
        return $this->data->getAllKeys();
    }

    /**
     * Injects the storage for the naming directory data.
     *
     * @param \AppserverIo\Storage\StorageInterface $data The naming directory data
     *
     * @return void
     */
    public function injectData(StorageInterface $data)
    {
        $this->data = $data;
    }

    /**
     * Injects the naming directory.
     *
     * @param \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory The naming directory instance
     *
     * @return void
     */
    public function injectNamingDirectory(NamingDirectoryInterface $namingDirectory)
    {
        $this->namingDirectory = $namingDirectory;
    }

    /**
     * Injects the storage for the managers.
     *
     * @param \AppserverIo\Storage\GenericStackable $managers The storage for the managers
     *
     * @return void
     */
    public function injectManagers(GenericStackable $managers)
    {
        $this->managers = $managers;
    }

    /**
     * Injects the storage for the virtual hosts.
     *
     * @param \AppserverIo\Storage\GenericStackable $virtualHosts The storage for the virtual hosts
     *
     * @return void
     */
    public function injectVirtualHosts(GenericStackable $virtualHosts)
    {
        $this->virtualHosts = $virtualHosts;
    }

    /**
     * Injects the storage for the class loaders.
     *
     * @param \AppserverIo\Storage\GenericStackable $classLoaders The storage for the class loaders
     *
     * @return void
     */
    public function injectClassLoaders(GenericStackable $classLoaders)
    {
        $this->classLoaders = $classLoaders;
    }

    /**
     * The initial context instance.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext The initial context instance
     *
     * @return void
     */
    public function injectInitialContext(ContextInterface $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * The mutex to lock/unlock resources during application deployment.
     *
     * @param integer $mutex The mutex to lock/unlock resources during application deployment
     *
     * @return void
     */
    public function injectMutex($mutex)
    {
        $this->mutex = $mutex;
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
     * Returns the application name (that has to be the class namespace, e.g. example)
     *
     * @return string The application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Create and return a new naming subdirectory with the attributes
     * of this one.
     *
     * @param string $name   The name of the new subdirectory
     * @param array  $filter Array with filters that will be applied when copy the attributes
     *
     * @return \AppserverIo\Appserver\Naming\NamingDirectory The new naming subdirectory
     */
    public function createSubdirectory($name, array $filter = array())
    {

        // create a new subdirectory instance
        $subdirectory = new NamingDirectory($name, $this);

        // copy the attributes specified by the filter
        if (sizeof($filter) > 0) {
            foreach ($this->getAllKeys() as $key => $value) {
                foreach ($filter as $pattern) {
                    if (fnmatch($pattern, $key)) {
                        $subdirectory->bind($key, $value);
                    }
                }
            }
        }

        // bind it the directory
        $this->bind($name, $subdirectory);

        // return the instance
        return $subdirectory;
    }

    /**
     * The unique identifier of this directory. That'll be build up
     * recursive from the scheme and the root directory.
     *
     * @return string The unique identifier
     * @see \AppserverIo\Storage\StorageInterface::getIdentifier()
     */
    public function getIdentifier()
    {

        // check if we've a parent directory
        if ($parent = $this->getParent()) {
            return $parent->getIdentifier() . '/' . $this->getName();
        }

        // if not, we MUST have a scheme, because we're root
        if ($scheme = $this->getScheme()) {
            return $scheme . ':' . $this->getName();
        }

        // the root node needs a scheme
        throw new NamingException(sprintf('Missing scheme for naming directory', $this->getName()));
    }

    /**
     * Returns the applications naming directory.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The applications naming directory interface
     */
    public function getNamingDirectory()
    {
        return $this->namingDirectory;
    }

    /**
     * Returns the applications naming directory.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The applications naming directory interface
     * @see \AppserverIo\Appserver\Application\Application::getNamingDirectory()
     */
    public function getParent()
    {
        return $this->getNamingDirectory();
    }

    /**
     * Returns the scheme.
     *
     * @return string The scheme we want to use
     */
    public function getScheme()
    {

        // if the parent directory has a schema, return this one
        if ($parent = $this->getParent()) {
            return $parent->getScheme();
        }

        // return our own schema
        return $this->scheme;
    }

    /**
     * Returns the absolute path to the servers document root directory
     *
     * @param string $directoryToAppend The directory to append to the base directory
     *
     * @return string The base directory with appended dir if given
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        $baseDirectory = $this->getNamingDirectory()->search('php:env/baseDirectory');
        if ($directoryToAppend != null) {
            $baseDirectory .= $directoryToAppend;
        }
        return $baseDirectory;
    }

    /**
     * Returns the absolute path to the web application base directory.
     *
     * @return string The path to the webapps folder
     */
    public function getWebappPath()
    {
        return $this->getAppBase() . DIRECTORY_SEPARATOR . $this->getName();
    }

    /**
     * Returns the absolute path to the applications base directory.
     *
     * @return string The app base directory
     */
    public function getAppBase()
    {
        return $this->getNamingDirectory()->search('php:env/appBase');
    }

    /**
     * Returns the absolute path to the applications temporary directory.
     *
     * @return string The app temporary directory
     */
    public function getTmpDir()
    {
        return $this->getNamingDirectory()->search(sprintf('php:env/%s/tmpDirectory', $this->getName()));
    }

    /**
     * Returns the absolute path to the applications session directory.
     *
     * @return string The app session directory
     */
    public function getSessionDir()
    {
        return $this->getTmpDir() . DIRECTORY_SEPARATOR . ApplicationInterface::SESSION_DIRECTORY;
    }

    /**
     * Returns the absolute path to the applications cache directory.
     *
     * @return string The app cache directory
     */
    public function getCacheDir()
    {
        return $this->getTmpDir() . DIRECTORY_SEPARATOR . ApplicationInterface::CACHE_DIRECTORY;
    }

    /**
     * Injects the username the application should be executed with.
     *
     * @return string The username
     */
    public function getUser()
    {
        return $this->getNamingDirectory()->search('php:env/user');
    }

    /**
     * Injects the groupname the application should be executed with.
     *
     * @return string The groupname
     */
    public function getGroup()
    {
        return $this->getNamingDirectory()->search('php:env/group');
    }

    /**
     * Returns the umask the application should create files/directories with.
     *
     * @return string The umask
     */
    public function getUmask()
    {
        return $this->getNamingDirectory()->search('php:env/umask');
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return object The service instance
     * @see \AppserverIo\Appserver\Application\Interfaces\ContextInterface::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ContextInterface The initial Context
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the applications available virtual host configurations.
     *
     * @return \AppserverIo\Storage\GenericStackable The available virtual host configurations
     */
    public function getVirtualHosts()
    {
        return $this->virtualHosts;
    }

    /**
     * Return the requested class loader instance
     *
     * @param string $identifier The unique identifier of the requested class loader
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface The class loader instance
     */
    public function getClassLoader($identifier)
    {
        if (isset($this->classLoaders[$identifier])) {
            return $this->classLoaders[$identifier];
        }
    }

    /**
     * Return the class loaders.
     *
     * @return \AppserverIo\Storage\GenericStackable The class loader instances
     */
    public function getClassLoaders()
    {
        return $this->classLoaders;
    }

    /**
     * Returns the manager instances.
     *
     * @return \AppserverIo\Storage\GenericStackable The manager instances
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * Return the requested manager instance.
     *
     * @param string $identifier The unique identifier of the requested manager
     *
     * @return \AppserverIo\Psr\Application\ManagerInterface The manager instance
     */
    public function getManager($identifier)
    {
        if (isset($this->managers[$identifier])) {
            return $this->managers[$identifier];
        }
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
        foreach ($this->getVirtualHosts() as $virtualHost) {

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
     * @param \AppserverIo\Appserver\Application\Interfaces\VirtualHostInterface $virtualHost The virtual host to add
     *
     * @return void
     */
    public function addVirtualHost(VirtualHostInterface $virtualHost)
    {
        $this->virtualHosts[] = $virtualHost;
    }

    /**
     * Injects an additional class loader.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface   $classLoader   A class loader to put on the class loader stack
     * @param \AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface $configuration The class loader's configuration
     *
     * @return void
     */
    public function addClassLoader(ClassLoaderInterface $classLoader, ClassLoaderNodeInterface $configuration = null)
    {
        if (!is_null($configuration)) {

            // load the lookup names from the configuration
            $lookupNames = $configuration->toLookupNames();

            // register the class loader with the default name (short class name OR @Annotation(name=****))
            $identifier = $lookupNames[AnnotationKeys::NAME];
            $this->bind($identifier, array(&$this, 'getClassLoader'), array($identifier));

            // register the bean with the name defined as @Annotation(beanInterface=****)
            if ($beanInterfaceAttribute = $lookupNames[AnnotationKeys::BEAN_INTERFACE]) {
                $this->bind($beanInterfaceAttribute, array(&$this, 'getClassLoader'), array($identifier));
            }

        } else {

            // our identifier will be the unqualified/short class name
            $reflectionClass = new \ReflectionClass($classLoader);
            $identifier = $reflectionClass->getShortName();
            $this->bind($identifier, array(&$this, 'getClassLoader'), array($identifier));
        }

        // register the manager instance itself
        $this->classLoaders[$identifier] = $classLoader;
    }

    /**
     * Injects manager instance and the configuration.
     *
     * @param \AppserverIo\Psr\Application\ManagerInterface                               $manager       A manager instance
     * @param \AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface $configuration The managers configuration
     *
     * @return void
     */
    public function addManager(ManagerInterface $manager, ManagerConfigurationInterface $configuration)
    {

        // load the lookup names from the configuration
        $lookupNames = $configuration->toLookupNames();

        // register the bean with the default name (short class name OR @Annotation(name=****))
        $identifier = $lookupNames[AnnotationKeys::NAME];
        $this->bind($identifier, array(&$this, 'getManager'), array($identifier));

        // register the bean with the name defined as @Annotation(beanInterface=****)
        if ($beanInterfaceAttribute = $lookupNames[AnnotationKeys::BEAN_INTERFACE]) {
            $this->bind($beanInterfaceAttribute, array(&$this, 'getManager'), array($identifier));
        }

        // register the bean with the name defined as @Annotation(beanName=****)
        if ($beanNameAttribute = $lookupNames[AnnotationKeys::BEAN_NAME]) {
            $this->getNamingDirectory()->bind($beanNameAttribute, array(&$this, 'getManager'), array($identifier));
        }

        // register the bean with the name defined as @Annotation(mappedName=****)
        if ($mappedNameAttribute = $lookupNames[AnnotationKeys::MAPPED_NAME]) {
            $this->getNamingDirectory()->bind($mappedNameAttribute, array(&$this, 'getManager'), array($identifier));
        }

        // register the manager instance itself
        $this->managers[$identifier] = $manager;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return void
     * @see \Thread::run()
     * @codeCoverageIgnore
     */
    public function connect()
    {

        // log a message that we now start to connect the application
        $this->getInitialContext()->getSystemLogger()->debug(sprintf('%s wait to be connected', $this->getName()));

        // synchronize the application startup
        $this->synchronized(function ($self) {

            // start the application
            $self->start();

            while ($self->connected === false) { // wait until we've been connected (classloaders and managers has been initialized)
                $self->wait(1000000 * Application::TIME_TO_LIVE);
            }

        }, $this);

        // log a message that we has successfully been connected now
        $this->getInitialContext()->getSystemLogger()->debug(sprintf('%s has successufully been connected', $this->getName()));
    }

    /**
     * Registers all class loaders injected to the applications in the opposite
     * order as they have been injected.
     *
     * @return void
     */
    public function registerClassLoaders()
    {
        foreach ($this->getClassLoaders() as $classLoader) {
            $classLoader->register(true, true);
        }
    }

    /**
     * Registers all managers in the application.
     *
     * @return void
     */
    public function initializeManagers()
    {
        foreach ($this->getManagers() as $manager) {

            // lock the mutex for manager initialization, because SPL usage
            // and IO read/write operations can propably cause segfaults!
            \Mutex::lock($this->mutex);

            // initialize the manager instance
            $manager->initialize($this);

            // unlock the mutex
            \Mutex::unlock($this->mutex);
        }
    }

    /**
     * This is the threads main() method that initializes the application with the autoloader and
     * instanciates all the necessary manager instances.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function run()
    {

        // register the class loaders
        $this->registerClassLoaders();

        // initialize the managers
        $this->initializeManagers();

        // initialize the profile logger and the thread context
        $profileLogger = null;
        if ($profileLogger = $this->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $profileLogger->appendThreadContext('application');
        }

        // we're connected now
        $this->connected = true;

        // we do nothing here
        while (true) {

            $this->synchronized(function ($self) {
                $self->wait(1000000 * Application::TIME_TO_LIVE);
            }, $this);

            if ($profileLogger) { // profile the application context
                $profileLogger->debug(sprintf('Application %s is running', $this->getName()));
            }
        }
    }
}
