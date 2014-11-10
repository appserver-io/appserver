<?php
/**
 * TechDivision\ApplicationServer\InitialContext
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Storage\GenericStackable;
use TechDivision\Storage\StorageInterface;
use TechDivision\ApplicationServer\InitialContext\ContextKeys;
use TechDivision\Configuration\Interfaces\NodeInterface;
use TechDivision\ApplicationServer\SplClassLoader;
use TechDivision\Application\Interfaces\ContextInterface;

/**
 * Class InitialContext
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class InitialContext implements ContextInterface
{

    /**
     * The storage instance
     *
     * @var \TechDivision\Storage\StorageInterface
     */
    protected $storage;

    /**
     * The server's logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $systemLogger;

    /**
     * The server's classLoading instance
     *
     * @var \TechDivision\ApplicationServer\SplClassLoader
     */
    protected $classLoader;

    /**
     * Initializes the context with the connection to the storage backend.
     *
     * @param \TechDivision\Configuration\Interfaces\NodeInterface $systemConfiguration The system configuration
     */
    public function __construct(NodeInterface $systemConfiguration)
    {

        // initialize the storage
        $initialContextNode = $systemConfiguration->getInitialContext();
        $storageNode = $initialContextNode->getStorage();
        $reflectionClass = $this->newReflectionClass($storageNode->getType());

        // create the storage instance
        $storage = $reflectionClass->newInstance();

        // append the storage servers registered in system configuration
        foreach ($storageNode->getStorageServers() as $storageServer) {
            $storage->addServer($storageServer->getAddress(), $storageServer->getPort(), $storageServer->getWeight());
        }

        // add the storage to the initial context
        $this->setStorage($storage);

        // initialize the class loader instance
        $classLoaderNode = $initialContextNode->getClassLoader();
        $reflectionClass = $this->newReflectionClass($classLoaderNode->getType());
        $this->setClassLoader($reflectionClass->newInstanceArgs(array($this)));

        // attach the system configuration to the initial context
        $this->setSystemConfiguration($systemConfiguration);
    }

    /**
     * Returns the storage instance.
     *
     * @param \TechDivision\Storage\StorageInterface $storage A storage instance
     *
     * @return \TechDivision\Storage\StorageInterface The storage instance
     */
    public function setStorage(StorageInterface $storage)
    {
        return $this->storage = $storage;
    }

    /**
     * Returns the storage instance.
     *
     * @return \TechDivision\Storage\StorageInterface The storage instance
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set's the initial context's class loader.
     *
     * @param \TechDivision\ApplicationServer\SplClassLoader $classLoader The class loader used
     *
     * @return void
     */
    public function setClassLoader(SplClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    /**
     * Return's the initial context's class loader.
     *
     * @return \TechDivision\ApplicationServer\SplClassLoader The class loader used
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }

    /**
     * Adds the system configuration to the inital context.
     *
     * @param object $systemConfiguration The system configuration
     *
     * @return void
     */
    public function setSystemConfiguration($systemConfiguration)
    {
        $this->setAttribute(ContextKeys::SYSTEM_CONFIGURATION, $systemConfiguration);
    }

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\Configuration\Interfaces\ConfigurationInterface The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->getAttribute(ContextKeys::SYSTEM_CONFIGURATION);
    }

    /**
     * Stores the passed key value pair in the initial context.
     *
     * @param string $key   The key to store the value under
     * @param mixed  $value The value to add to the inital context
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->storage->set($key, $value);
    }

    /**
     * Returns the value with the passed key from the initial context.
     *
     * @param string $key The key of the value to return
     *
     * @return mixed The value stored in the initial context
     */
    public function getAttribute($key)
    {
        return $this->storage->get($key);
    }

    /**
     * Removes the attribute with the passed key from the initial context.
     *
     * @param string $key The key of the value to delete
     *
     * @return void
     */
    public function removeAttribute($key)
    {
        $this->storage->remove($key);
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection instance for
     *
     * @return \ReflectionClass The reflection instance
     */
    public function newReflectionClass($className)
    {
        return new \ReflectionClass($className);
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @todo Has to be refactored to avoid registering autoloader on every call
     */
    public function newInstance($className, array $args = array())
    {
        // create and return a new instance
        $reflectionClass = $this->newReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }

    /**
     * Returns a new instance of the passed API service.
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     */
    public function newService($className)
    {
        return $this->newInstance($className, array(
            $this
        ));
    }

    /**
     * Set's the system logger instance.
     *
     * @param \Psr\Log\LoggerInterface $systemLogger The system logger
     *
     * @return void
     */
    public function setSystemLogger($systemLogger)
    {
        $this->systemLogger = $systemLogger;
    }

    /**
     * Set's logger array
     *
     * @param array $loggers The loggers array to set
     *
     * @return void
     */
    public function setLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    /**
     * Return
     *
     * @return array
     */
    public function getLoggers()
    {
        return $this->loggers;
    }

    /**
     * Get's the logger by given name
     *
     * @param string $loggerName the loggers name
     *
     * @return \Psr\Log\LoggerInterface|null The logger instance
     */
    public function getLogger($loggerName)
    {
        if (isset($this->loggers[$loggerName])) {
            return $this->loggers[$loggerName];
        }
    }

    /**
     * Return's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getSystemLogger()
    {
        return $this->systemLogger;
    }
}
