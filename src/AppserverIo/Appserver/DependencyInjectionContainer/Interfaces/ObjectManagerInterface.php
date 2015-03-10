<?php

/**
 * \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ObjectManagerInterface
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer\Interfaces;

use AppserverIo\Storage\StorageInterface;
use AppserverIo\Psr\Deployment\DescriptorInterface;

/**
 * Interface for all object manager implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ObjectManagerInterface
{

    /**
     * Unique identifier for the object manager.
     *
     * @var string
     */
    const IDENTIFIER = 'ObjectManagerInterface';

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
    public function addObjectDescriptor(DescriptorInterface $objectDescriptor, $merge = false);

    /**
     * Returns the object descriptor if we've registered it.
     *
     * @param string $className The class name we want to return the object descriptor for
     *
     * @return \AppserverIo\Psr\Deployment\DescriptorInterface|null The requested object descriptor instance
     * @throws \AppserverIo\Appserver\DependencyInjectionContainer\UnknownObjectDescriptorException Is thrown if someone tries to access an unknown object desciptor
     */
    public function getObjectDescriptor($className);

    /**
     * Returns the storage with the object descriptors.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the object descriptors
     */
    public function getObjectDescriptors();

    /**
     * Query if we've an object descriptor for the passed class name.
     *
     * @param string $className The class name we query for a object descriptor
     *
     * @return boolean TRUE if an object descriptor has been registered, else FALSE
     */
    public function hasObjectDescriptor($className);

    /**
     * Inject the storage for the object descriptors.
     *
     * @param \AppserverIo\Storage\StorageInterface $objectDescriptors The storage for the object descriptors
     *
     * @return void
     */
    public function injectObjectDescriptors(StorageInterface $objectDescriptors);

    /**
     * Registers the passed object descriptor under its class name.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor The object descriptor to set
     *
     * @return void
     */
    public function setObjectDescriptor(DescriptorInterface $objectDescriptor);
}
