<?php

/**
 * AppserverIo\Appserver\Core\AbstractManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use Psr\Container\ContainerInterface;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\Di\ProviderInterface;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Application\ManagerConfigurationInterface;
use AppserverIo\Psr\Naming\InitialContext as NamingDirectory;

/**
 * Abstract manager implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Storage\StorageInterface                      $data                 Storage container for arbitrary data
 * @property \AppserverIo\Psr\Naming\InitialContext                     $initialContext       The initial context of our naming directory
 * @property \AppserverIo\Psr\Application\ApplicationInterface          $application          The application to manage
 * @property \AppserverIo\Psr\Application\ManagerConfigurationInterface $managerConfiguration The application to manage
 */
abstract class AbstractManager extends GenericStackable implements ManagerInterface, ContainerInterface
{

    /**
     * Inject the configuration for this manager.
     *
     * @param \AppserverIo\Psr\Application\ManagerConfigurationInterface $managerConfiguration The managers configuration
     *
     * @return void
     */
    public function injectManagerConfiguration(ManagerConfigurationInterface $managerConfiguration)
    {
        $this->managerConfiguration = $managerConfiguration;
    }

    /**
     * Inject the data storage.
     *
     * @param \AppserverIo\Storage\StorageInterface $data The data storage to use
     *
     * @return void
     */
    public function injectData(StorageInterface $data)
    {
        $this->data = $data;
    }

    /**
     * Inject the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Return the storage with the naming directory.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the naming directory
     */
    public function getNamingDirectory()
    {
        return $this->getApplication()->getNamingDirectory();
    }

    /**
     * The global naming directory.
     *
     * @param \AppserverIo\Psr\Naming\InitialContext $initialContext The global naming directory
     *
     * @return void
     */
    public function injectInitialContext(NamingDirectory $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Returns the global naming directory.
     *
     * @return \AppserverIo\Psr\Naming\InitialContext The global naming directory
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the absolute path to the web application.
     *
     * @return string The absolute path
     */
    public function getWebappPath()
    {
        return $this->getApplication()->getWebappPath();
    }

    /**
     * Returns the absolute path to the application directory.
     *
     * @return string The absolute path to the application directory
     */
    public function getAppBase()
    {
        return $this->getApplication()->getAppBase();
    }

    /**
     * Returns the absolute path to the application server's base directory.
     *
     * @param string $directoryToAppend A directory to append to the base directory
     *
     * @return string The absolute path the application server's base directory
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        return $this->getApplication()->getBaseDirectory($directoryToAppend);
    }

    /**
     * Query's whether or not the attribute is available.
     *
     * @param string $key The key of the attribute to query for
     *
     * @return boolean TRUE if the attribute is set, else FALSE
     */
    public function hasAttribute($key)
    {
        return $this->data->has($key);
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
     * Returns a new reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     */
    public function newReflectionClass($className)
    {
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER)->newReflectionClass($className);
    }

    /**
     * Return's the manager configuration.
     *
     * @return \AppserverIo\Psr\Application\ManagerConfigurationInterface The manager configuration
     */
    public function getManagerConfiguration()
    {
        return $this->managerConfiguration;
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \AppserverIo\Psr\Di\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClass($className)
    {
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER)->getReflectionClass($className);
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param object $instance The instance to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \AppserverIo\Psr\Di\ProviderInterface::newReflectionClass()
     * @see \AppserverIo\Psr\Di\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClassForObject($instance)
    {
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER)->getReflectionClassForObject($instance);
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
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER)->newInstance($className, $args);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for
     *
     * @throws \Psr\Container\NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws \Psr\Container\ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER)->get($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning TRUE does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean TRUE if an entroy for the given identifier exists, else FALSE
     */
    public function has($id)
    {
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER)->has($id);
    }

    /**
     * Register's the passed value with the passed ID.
     *
     * @param string $id    The ID of the value to add
     * @param string $value The value to add
     *
     * @return void
     * @throws \AppserverIo\Appserver\DependencyInjectionContainer\ContainerException Is thrown, if a value with the passed key has already been added
     */
    public function set($id, $value)
    {
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER)->set($id, $value);
    }

    /**
     * Query's whether or not an instance of the passed already exists.
     *
     * @param string $id Identifier of the entry to look for
     *
     * @return boolean TRUE if an instance exists, else FALSE
     */
    public function exists($id)
    {
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER)->exists($id);
    }

    /**
     * Lifecycle callback that'll be invoked after the application has been started.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::postStartup()
     */
    public function postStartup(ApplicationInterface $application)
    {
    }

    /**
     * Parse the manager's object descriptors.
     *
     * @return void
     */
    public function parseObjectDescriptors()
    {

        // load the manager configuration
        /** @var \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerConfiguration */
        $managerConfiguration = $this->getManagerConfiguration();

        // load the object description configuration
        $objectDescription = $managerConfiguration->getObjectDescription();

        // initialize the parsers and start initializing the object descriptors
        /** @var \AppserverIo\Appserver\Core\Api\Node\ParserNodeInterface */
        foreach ($objectDescription->getParsers() as $parserConfiguration) {
            // query whether or not a factory has been configured
            if ($parserFactoryConfiguration = $parserConfiguration->getFactory()) {
                // create a reflection class from the parser factory
                $reflectionClass = new ReflectionClass($parserFactoryConfiguration);
                $factory = $reflectionClass->newInstance();

                // create the parser instance and start parsing
                $parser = $factory->createParser($parserConfiguration, $this);
                $parser->parse();
            }
        }
    }

    /**
     * Returns the managers object descriptors to use.
     *
     * @return array The object descriptors
     */
    public function getDescriptors()
    {

        // load the manager configuration
        /** @var \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerConfiguration */
        $managerConfiguration = $this->getManagerConfiguration();

        // load the object description configuration
        $objectDescription = $managerConfiguration->getObjectDescription();

        // return the configured object descriptors
        return $objectDescription->getDescriptors();
    }

    /**
     * A dummy functionality to implement the stop functionality.
     *
     * @return void
     * \AppserverIo\Psr\Application\ManagerInterface::stop()
     */
    public function stop()
    {
        error_log(sprintf('Now shutdown manager "%s"', $this->getIdentifier()));
    }
}
