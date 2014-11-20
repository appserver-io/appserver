<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\ServiceRegistry
 *
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

namespace AppserverIo\Appserver\PersistenceContainer;

use TechDivision\Storage\StorageInterface;
use TechDivision\Storage\GenericStackable;
use TechDivision\Storage\StackableStorage;

/**
 * The abstract service registry as base for implementations that handles applications services.
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
abstract class ServiceRegistry extends GenericStackable implements ServiceContext
{

    /**
     * Inject the data storage.
     *
     * @param \TechDivision\Storage\StackableStorage $data The data storage to use
     *
     * @return void
     */
    public function injectData(StorageInterface $data)
    {
        $this->data = $data;
    }

    /**
     * Injects the absolute path to the web application.
     *
     * @param string $webappPath The absolute path to this web application
     *
     * @return void
     */
    public function injectWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Injects the service locator to locate the service.
     *
     * @param \AppserverIo\Appserver\MessageQueue\ResourceLocator $serviceLocator The service locator
     *
     * @return void
     */
    public function injectServiceLocator(ServiceResourceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Injects the storage for the services.
     *
     * @param \TechDivision\Storage\StorageInterface $services The storage for the services
     *
     * @return void
     */
    public function injectServices(StorageInterface $services)
    {
        $this->services = $services;
    }

    /**
     * Returns the absolute path to the web application.
     *
     * @return string The absolute path
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Return the service locator instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\ServiceLocator The service locator instance
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Return the storage with the services.
     *
     * @return \TechDivision\Storage\StorageInterface The storage with the services
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Tries to locate and return the service with the passed name.
     *
     * @param string $serviceName The name of the requested service
     * @param array  $args        The arguments passed to the service providers constructor
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\ServiceProvider The requested service instance
     */
    public function locate($serviceName, array $args = array())
    {
        return $this->getServiceLocator()->locate($this, $serviceName);
    }

    /**
     * Registers the value with the passed key in the container.
     *
     * @param string $key   The key to register the value with
     * @param object $value The value to register
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->data->set($key, $value);
    }

    /**
     * Returns the attribute with the passed key from the container.
     *
     * @param string $key The key the requested value is registered with
     *
     * @return mixed|null The requested value if available
     */
    public function getAttribute($key)
    {
        if ($this->data->has($key)) {
            return $this->data->get($key);
        }
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
     */
    public function newInstance($className, array $args = array())
    {
        $reflectionClass = $this->newReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }
}
