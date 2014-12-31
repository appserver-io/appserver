<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\BeanDescriptor
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
 * @subpackage DependencyInjectionContainer
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer\Description;

use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Resource;

/**
 * Abstract class for all bean descriptors.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage DependencyInjectionContainer
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class BeanDescriptor implements BeanDescriptorInterface
{

    /**
     * The bean name.
     *
     * @var string
     */
    protected $name;

    /**
     * The beans class name.
     *
     * @var string
     */
    protected $className;

    /**
     * The configurable bean name.
     *
     * @var string
     */
    protected $beanName;

    /**
     * The bean interface.
     *
     * @var string
     */
    protected $beanInterface;

    /**
     * The mapped name.
     *
     * @var string
     */
    protected $mappedName;

    /**
     * The beans description.
     *
     * @var string
     */
    protected $description;

    /**
     * The array with the EPB references.
     *
     * @var array
     */
    protected $epbReferences = array();

    /**
     * Sets the bean name.
     *
     * @param string $name The bean name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the bean name.
     *
     * @return string The bean name
     */
    public function getName()
    {
        return $this->name;
    }

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
     * Sets the beans description.
     *
     * @param string $description The beans description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the beans description.
     *
     * @return string The beans description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the configurable bean name.
     *
     * @param string $beanName The configurable bean name
     *
     * @return void
     */
    public function setBeanName($beanName)
    {
        $this->beanName = $beanName;
    }

    /**
     * Returns the configurable bean name.
     *
     * @return string The configurable bean name
     */
    public function getBeanName()
    {
        return $this->beanName;
    }

    /**
     * Sets the bean interface.
     *
     * @param string $beanInterface The bean interface
     *
     * @return void
     */
    public function setBeanInterface($beanInterface)
    {
        $this->beanInterface = $beanInterface;
    }

    /**
     * Returns the bean interface.
     *
     * @return string The bean interface
     */
    public function getBeanInterface()
    {
        return $this->beanInterface;
    }

    /**
     * Sets the mapped name.
     *
     * @param string $mappedName The mapped name
     *
     * @return void
     */
    public function setMappedName($mappedName)
    {
        $this->mappedName = $mappedName;
    }

    /**
     * Returns the mapped name.
     *
     * @return string The mapped name
     */
    public function getMappedName()
    {
        return $this->mappedName;
    }

    /**
     * Adds a EPB reference configuration.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface $epbReference The EPB reference configuration
     *
     * @return void
     */
    public function addEpbReference(EpbReferenceDescriptorInterface $epbReference)
    {
        $this->epbReferences[$epbReference->getRefName()] = $epbReference;
    }

    /**
     * Sets the array with the EPB references.
     *
     * @param array $epbReferences The EPB references
     *
     * @return void
     */
    public function setEpbReferences(array $epbReferences)
    {
        $this->epbReferences = $epbReferences;
    }

    /**
     * The array with the EPB references.
     *
     * @return array The EPB references
     */
    public function getEpbReferences()
    {
        return $this->epbReferences;
    }

    /**
     * Returns a new annotation instance for the passed reflection class.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Lang\Reflection\AnnotationInterface The reflection annotation
     */
    protected abstract function newAnnotationInstance(ClassInterface $reflectionClass);

    /**
     * Initializes the bean configuration instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return void
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // create a new annotation instance
        $reflectionAnnotation = $this->newAnnotationInstance($reflectionClass);

        // load class name
        $this->setClassName($reflectionClass->getName());

        // initialize the annotation instance
        $annotationInstance = $reflectionAnnotation->newInstance(
            $reflectionAnnotation->getAnnotationName(),
            $reflectionAnnotation->getValues()
        );

        // load the default name to register in naming directory
        if ($nameAttribute = $annotationInstance->getName()) {
            $this->setName($nameAttribute);
        } else { // if @Annotation(name=****) is NOT set, we use the short class name by default
            $this->setName($reflectionClass->getShortName());
        }

        // register the bean with the interface defined as @Annotation(beanInterface=****)
        if ($beanInterfaceAttribute = $annotationInstance->getBeanInterface()) {
            $this->setBeanInterface($beanInterfaceAttribute);
        }

        // register the bean with the name defined as @Annotation(beanName=****)
        if ($beanNameAttribute = $annotationInstance->getBeanName()) {
            $this->setBeanName($beanNameAttribute);
        }

        // register the bean with the name defined as @Annotation(mappedName=****)
        if ($mappedNameAttribute = $annotationInstance->getMappedName()) {
            $this->setMappedName($mappedNameAttribute);
        }

        // we've to check for property annotations that references EPB or resources
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if ($epbReference = EpbReferenceDescriptor::newDescriptorInstance()->fromReflectionProperty($reflectionProperty)) {
                $this->addEpbReference($epbReference);
            }
        }

        // we've to check for method annotations that references EPB or resources
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if ($epbReference = EpbReferenceDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod)) {
                $this->addEpbReference($epbReference);
            }
        }
    }

    /**
     * Initializes a bean configuration instance from the passed deployment descriptor node.
     *
     * @param \SimpleXmlElement $node The deployment node with the bean configuration
     *
     * @return void
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // query for the class name and set it
        if ($className = (string) $node->{'epb-class'}) {
            $this->setClassName($className);
        }

        // query for the name and set it
        if ($name = (string) $node->{'epb-name'}) {
            $this->setName($name);
        }

        // query for the bean interface and set it
        if ($beanInterface = (string) $node->{'bean-interface'}) {
            $this->setBeanInterface($beanInterface);
        }

        // query for the bean name and set it
        if ($beanName = (string) $node->{'bean-name'}) {
            $this->setBeanName($beanName);
        }

        // query for the mapped name and set it
        if ($mappedName = (string) $node->{'mapped-name'}) {
            $this->setMappedName($mappedName);
        }

        // query for the description and set it
        if ($description = (string) $node->{'description'}) {
            $this->setDescription($description);
        }

        // initialize the enterprise bean references
        foreach ($node->xpath('epb-ref') as $epbReference) {
            $this->addEpbReference(EpbReferenceDescriptor::newDescriptorInstance()->fromDeploymentDescriptor($epbReference));
        }
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface $beanDescriptor The configuration to merge
     *
     * @return void
     */
    public function merge(BeanDescriptorInterface $beanDescriptor)
    {

        // check if the classes are equal
        if ($this->getClassName() !== $beanDescriptor->getClassName()) {
            throw new EnterpriseBeansException(
                sprintf('You try to merge a bean configuration for % with %s', $beanDescriptor->getClassName(), $this->getClassName())
            );
        }

        // merge the name
        if ($name = $beanDescriptor->getName()) {
            $this->setName($name);
        }

        // merge the bean interface
        if ($beanInterface = $beanDescriptor->getBeanInterface()) {
            $this->setBeanInterface($beanInterface);
        }

        // merge the bean name
        if ($beanName = $beanDescriptor->getBeanName()) {
            $this->setBeanName($beanName);
        }

        // merge the description
        if ($description = $beanDescriptor->getDescription()) {
            $this->setDescription($description);
        }

        // merge the EPB references
        foreach ($beanDescriptor->getEpbReferences() as $epbReference) {
            $this->addEpbReference($epbReference);
        }
    }
}
