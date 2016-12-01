<?php

/**
 * \AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager
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

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Storage\StorageInterface;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Di\UnknownObjectDescriptorException;
use AppserverIo\Psr\Deployment\DescriptorInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The object manager is necessary to load and provides information about all
 * objects related with the application itself.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property array                                 $configuredDescriptors Descriptors used to parse deployment descriptors and annotations from the managers configuration
 * @property \AppserverIo\Storage\StorageInterface $objectDescriptors     Storage for our collected descriptors
 */
class ObjectManager extends AbstractManager implements ObjectManagerInterface
{

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
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor The object descriptor to add
     * @param boolean                                         $merge            TRUE if we want to merge with an existing object descriptor
     *
     * @return void
     */
    public function addObjectDescriptor(DescriptorInterface $objectDescriptor, $merge = false)
    {

        // query whether we've to merge the configuration found in annotations
        if ($this->hasObjectDescriptor($objectDescriptor->getClassName()) && $merge) {
            // load the existing descriptor
            $existingDescriptor = $this->getObjectDescriptor($objectDescriptor->getClassName());
            $existingDescriptorType = get_class($existingDescriptor);
            // log on info level to make overwriting more obvious
            $this->getApplication()->getInitialContext()->getSystemLogger()->info(
                sprintf(
                    'Overwriting descriptor %s of webapp %s from XML configuration.',
                    $existingDescriptor->getName(),
                    $this->getApplication()->getName()
                )
            );

            // Merge the descriptors. XML configuration overrides values from annotation but we have to make sure
            // that the descriptors are of the same type. If not we have to take the XML descriptor as our new base descriptor
            // and merge the annotations (but XML still has to be dominant)
            $mergedDescriptor = null;
            if (!$objectDescriptor instanceof $existingDescriptorType) {
                // create a temporary clone of the object (XML) descriptor to merge the existing descriptor into an instance with the same type
                $mergedDescriptor = clone $objectDescriptor;
                $mergedDescriptor->merge($existingDescriptor);
                $mergedDescriptor->merge($objectDescriptor);

            } else {
                // merge the object descriptor into the existing one
                $mergedDescriptor = $existingDescriptor;
                $mergedDescriptor->merge($objectDescriptor);
            }

            // re-register the merged descriptor
            $this->setObjectDescriptor($mergedDescriptor);

        } else {
            // register the object descriptor
            $this->setObjectDescriptor($objectDescriptor);
        }
    }

    /**
     * Registers the passed object descriptor under its class name.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor The object descriptor to set
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
     * @return \AppserverIo\Psr\Deployment\DescriptorInterface|null The requested object descriptor instance
     * @throws \AppserverIo\Psr\Di\UnknownObjectDescriptorException Is thrown if someone tries to access an unknown object desciptor
     */
    public function getObjectDescriptor($className)
    {

        // query if we've an object descriptor registered
        if ($this->hasObjectDescriptor($className)) {
            // return the object descriptor
            return $this->objectDescriptors->get($className);
        }

        // throw an exception is object descriptor has not been registered
        throw new UnknownObjectDescriptorException(sprintf('Object Descriptor for class %s has not been registered', $className));
    }

    /**
     * Returns the identifier for the object manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return ObjectManagerInterface::IDENTIFIER;
    }
}
