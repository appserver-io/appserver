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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer\Description;

use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ResReferenceDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException;

/**
 * Abstract class for all bean descriptors.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
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
     * The array with the EPB references.
     *
     * @var array
     */
    protected $epbReferences = array();

    /**
     * The array with the resource references.
     *
     * @var array
     */
    protected $resReferences = array();

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
     * Adds a EPB reference configuration.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface $epbReference The EPB reference configuration
     *
     * @return void
     */
    public function addEpbReference(EpbReferenceDescriptorInterface $epbReference)
    {
        $this->epbReferences[$epbReference->getName()] = $epbReference;
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
     * Adds a resource reference configuration.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ResReferenceDescriptorInterface $resReference The resource reference configuration
     *
     * @return void
     */
    public function addResReference(ResReferenceDescriptorInterface $resReference)
    {
        $this->resReferences[$resReference->getName()] = $resReference;
    }

    /**
     * Sets the array with the resource references.
     *
     * @param array $resReferences The resource references
     *
     * @return void
     */
    public function setResReferences(array $resReferences)
    {
        $this->resReferences = $resReferences;
    }

    /**
     * The array with the resource references.
     *
     * @return array The resource references
     */
    public function getResReferences()
    {
        return $this->resReferences;
    }

    /**
     * Returns an array with the merge EBP and resource references.
     *
     * @return array The array with the merge all bean references
     */
    public function getReferences()
    {
        return array_merge($this->epbReferences, $this->resReferences);
    }

    /**
     * Returns a new annotation instance for the passed reflection class.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Lang\Reflection\AnnotationInterface The reflection annotation
     */
    abstract protected function newAnnotationInstance(ClassInterface $reflectionClass);

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
        } else {
            // if @Annotation(name=****) is NOT set, we use the short class name by default
            $this->setName($reflectionClass->getShortName());
        }

        // we've to check for property annotations that references EPB or resources
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            // load the EPB references
            if ($epbReference = EpbReferenceDescriptor::newDescriptorInstance()->fromReflectionProperty($reflectionProperty)) {
                $this->addEpbReference($epbReference);
            }

            // load the resource references
            if ($resReference = ResReferenceDescriptor::newDescriptorInstance()->fromReflectionProperty($reflectionProperty)) {
                $this->addResReference($resReference);
            }
        }

        // we've to check for method annotations that references EPB or resources
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            // load the EPB references
            if ($epbReference = EpbReferenceDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod)) {
                $this->addEpbReference($epbReference);
            }

            // load the resource references
            if ($resReference = ResReferenceDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod)) {
                $this->addResReference($resReference);
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
        $node->registerXPathNamespace('a', 'http://www.appserver.io/appserver');

        // query for the class name and set it
        if ($className = (string) $node->{'epb-class'}) {
            $this->setClassName($className);
        }

        // query for the name and set it
        if ($name = (string) $node->{'epb-name'}) {
            $this->setName($name);
        }

        // initialize the enterprise bean references
        foreach ($node->xpath('a:epb-ref') as $epbReference) {
            $this->addEpbReference(EpbReferenceDescriptor::newDescriptorInstance()->fromDeploymentDescriptor($epbReference));
        }

        // initialize the resource references
        foreach ($node->xpath('a:res-ref') as $resReference) {
            $this->addResReference(ResReferenceDescriptor::newDescriptorInstance()->fromDeploymentDescriptor($resReference));
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

        // merge the EPB references
        foreach ($beanDescriptor->getEpbReferences() as $epbReference) {
            $this->addEpbReference($epbReference);
        }

        // merge the EPB references
        foreach ($beanDescriptor->getResReferences() as $resReference) {
            $this->addResReference($resReference);
        }
    }
}
