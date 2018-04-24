<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Description\PersistenceUnitDescriptor
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
 * @link      https://github.com/appserver-io/description
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Description;

use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Description\DescriptorUtil;
use AppserverIo\Description\FactoryDescriptor;
use AppserverIo\Description\AbstractNameAwareDescriptor;
use AppserverIo\Description\Configuration\ConfigurationInterface;
use AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface;
use AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PersistenceUnit;
use AppserverIo\Psr\EnterpriseBeans\Description\FactoryDescriptorInterface;

/**
 * Descriptor implementation for a persistence unit.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/description
 * @link      http://www.appserver.io
 */
class PersistenceUnitDescriptor extends AbstractNameAwareDescriptor implements PersistenceUnitDescriptorInterface
{

    /**
     * The beans class name.
     *
     * @var string
     */
    protected $className;

    /**
     * The factory that creates the bean.
     *
     * @var \AppserverIo\Psr\EnterpriseBeans\Description\FactoryDescriptorInterface
     */
    protected $factory;

    /**
     * Sets the beans class name.
     *
     * @param string $className The beans class name
     *
     * @return void
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Returns the beans class name.
     *
     * @return string The beans class name
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Sets the factory that creates the bean.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\FactoryDescriptorInterface $factory The bean's factory
     *
     * @return void
     */
    public function setFactory(FactoryDescriptorInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Returns the factory that creates the bean.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\Description\FactoryDescriptorInterface The bean's factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Description\PersistenceUnitDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new PersistenceUnitDescriptor();
    }

    /**
     * Returns a new annotation instance for the passed reflection class.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Lang\Reflection\AnnotationInterface The reflection annotation
     */
    protected function newAnnotationInstance(ClassInterface $reflectionClass)
    {
        return $reflectionClass->getAnnotation(PersistenceUnit::ANNOTATION);
    }

    /**
     * Initializes a bean configuration instance from the passed configuration node.
     *
     * @param \AppserverIo\Description\Configuration\ConfigurationInterface $configuration The bean configuration
     *
     * @return void
     */
    public function fromConfiguration(ConfigurationInterface $configuration)
    {

        // query whether or not we've preference configuration
        if (!$configuration instanceof PersistenceUnitNodeInterface) {
            return;
        }

        // query for the class name and set it
        if ($className = (string) $configuration->getType()) {
            $this->setClassName(DescriptorUtil::trim($className));
        }

        // query for the name and set it
        if ($name = (string) $configuration->getName()) {
            $this->setName(DescriptorUtil::trim($name));
        }

        // merge the shared flag
        if ($shared = $configuration->getShared()) {
            $this->setShared(Boolean::valueOf(new String($shared))->booleanValue());
        }

        // load the factory information from the reflection class
        if ($configuration->getFactory()) {
            // initialize and set the factory descriptor for the persistence unit
            $factoryDescriptor = FactoryDescriptor::newDescriptorInstance();
            $factoryDescriptor->setName(sprintf('%sFactory', $name));
            $factoryDescriptor->setMethod('factory');
            $this->setFactory($factoryDescriptor);
        }

        // return the instance
        return $this;
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $persistenceUnitDescriptor The configuration to merge
     *
     * @return void
     */
    public function merge(PersistenceUnitDescriptorInterface $persistenceUnitDescriptor)
    {

        // check if the classes are equal
        if ($this->getName() !== $persistenceUnitDescriptor->getName()) {
            throw new EnterpriseBeansException(
                sprintf('You try to merge a persistence unit configuration for "%s" with "%s"', $persistenceUnitDescriptor->getName(), $this->getName())
            );
        }

        // merge the class name
        if ($className = $persistenceUnitDescriptor->getClassName()) {
            $this->setClassName($className);
        }

        // merge the factory
        if ($factory = $persistenceUnitDescriptor->getFactory()) {
            $this->setFactory($factory);
        }

        // merge the shared flag
        $this->setShared($persistenceUnitDescriptor->isShared());
    }
}
