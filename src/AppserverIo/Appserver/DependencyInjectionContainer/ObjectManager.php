<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager
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

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Storage\StorageInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Parsers\BeanDescriptor;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\DescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ObjectManagerInterface;
use AppserverIo\Collections\IndexOutOfBoundsException;

/**
 * The object manager is necessary to load and provides information about all
 * objects related with the application itself.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class ObjectManager extends GenericStackable implements ObjectManagerInterface, ManagerInterface
{

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
     * Inject the storage for the object descriptors.
     *
     * @param \AppserverIo\Storage\StorageInterface $objectDescriptors The storage for the object descriptors
     *
     * @return void
     */
    public function injectObjectDescriptors(StorageInterface $objectDescriptors)
    {
        $this->objectDescriptors = $objectDescriptors;
    }

    /**
     * Inject the descriptors used to parse deployment descriptors and annotations
     * from the managers configuration.
     *
     * @param array $configuredDescriptors The descriptors to use
     *
     * @return void
     */
    public function injectConfiguredDescriptors(array $configuredDescriptors)
    {
        $this->configuredDescriptors = $configuredDescriptors;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the storage with the object descriptors.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the object descriptors
     */
    public function getObjectDescriptors()
    {
        return $this->objectDescriptors;
    }

    /**
     * Returns the descriptors used to parse deployment descriptors and annotations.
     *
     * @return array The descriptors to use
     */
    public function getConfiguredDescriptors()
    {
        return $this->configuredDescriptors;
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
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function initialize(ApplicationInterface $application)
    {
    }

    /**
     * Adds the passed object descriptor to the object manager. If the merge flag is TRUE, then
     * we check if already an object descriptor for the class exists before they will be merged.
     *
     * When we merge object descriptors this means, that the values of the passed descriptor
     * will override the existing ones.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\DescriptorInterface $objectDescriptor The object descriptor to add
     * @param boolean                                                                            $merge            TRUE if we want to merge with an existing object descriptor
     *
     * @return void
     */
    public function addObjectDescriptor(DescriptorInterface $objectDescriptor, $merge = false)
    {

        // query whether we've to merge the configuration found in annotations
        if ($override && $this->hasObjectDescriptor($objectDescriptor->getClassName())) {

            // load the existing descriptor
            $existingDescriptor = $this->getObjectDescriptor($objectDescriptor->getClassName());

            // merge the descriptor => XML configuration overrides values from annotation
            $existingDescriptor->merge($objectDescriptor);

            // re-register the merged descriptor
            $this->setObjectDescriptor($existingDescriptor);

        } else {

            // register the object descriptor
            $this->setObjectDescriptor($objectDescriptor);
        }
    }

    /**
     * Registers the passed object descriptor under its class name.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\DescriptorInterface $objectDescriptor The object descriptor to set
     *
     * @return void
     */
    public function setObjectDescriptor(DescriptorInterface $objectDescriptor)
    {
        $this->objectDescriptors->set($objectDescriptor->getClassName(), $objectDescriptor);
    }

    /**
     * Query if we've an object descriptor for the passed class name.
     *
     * @param string $className The class name we query for a object descriptor
     *
     * @return boolean TRUE if an object descriptor has been registered, else FALSE
     */
    public function hasObjectDescriptor($className)
    {
        return $this->objectDescriptors->has($className);
    }

    /**
     * Returns the object descriptor if we've registered it.
     *
     * @param string $className The class name we want to return the object descriptor for
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\DescriptorInterface|null The requested object descriptor instance
     * @throws \AppserverIo\Appserver\DependencyInjectionContainer\UnknownObjectDescriptorException Is thrown if someone tries to access an unknown object desciptor
     */
    public function getObjectDescriptor($className)
    {

        // query if we've an object descriptor registered
        if ($this->hasObjectDescriptor($className) === false) { // return the object descriptor
            return $this->objectDescriptors->get($className);
        }

        // throw an exception is object descriptor has not been registered
        throw new UnknownObjectDescriptorException(sprintf('Object Descriptor for class %s has not been registered', $className));
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
        return $this->getApplication()->search('ProviderInterface')->newReflectionClass($className);
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClass($className)
    {
        return $this->getApplication()->search('ProviderInterface')->getReflectionClass($className);
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param object $instance The instance to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::newReflectionClass()
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClassForObject($instance)
    {
        return $this->getApplication()->search('ProviderInterface')->getReflectionClassForObject($instance);
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string      $className The fully qualified class name to return the instance for
     * @param string|null $sessionId The session-ID, necessary to inject stateful session beans (SFBs)
     * @param array       $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newInstance($className, $sessionId = null, array $args = array())
    {
        return $this->getApplication()->search('ProviderInterface')->newInstance($className, $sessionId, $args);
    }

    /**
     * Initializes the manager instance.
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return ObjectManagerInterface::IDENTIFIER;
    }
}
