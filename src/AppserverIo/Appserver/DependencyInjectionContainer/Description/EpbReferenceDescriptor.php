<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\EpbReferenceDescriptor
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
use AppserverIo\Lang\Reflection\MethodInterface;
use AppserverIo\Lang\Reflection\PropertyInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface;

/**
 * Utility class that stores a beans reference configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EpbReferenceDescriptor implements EpbReferenceDescriptorInterface
{

    /**
     * Prefix for EPB references.
     *
     * @var string
     */
    const REF_DIRECTORY = 'env';

    /**
     * The reference name.
     *
     * @var string
     */
    protected $name;

    /**
     * The beans description.
     *
     * @var string
     */
    protected $description;

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
     * The lookup name.
     *
     * @var string
     */
    protected $lookup;

    /**
     * The injection target specification.
     *
     * @var \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface
     */
    protected $injectionTarget;

    /**
     * Sets the reference name.
     *
     * @param string $name The reference name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the reference name.
     *
     * @return string The reference name
     */
    public function getName()
    {
        return $this->name;
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
     * Sets the lookup name.
     *
     * @param string $lookup The lookup name
     *
     * @return void
     */
    public function setLookup($lookup)
    {
        $this->lookup = $lookup;
    }

    /**
     * Returns the lookup name.
     *
     * @return string The lookup name
     */
    public function getLookup()
    {
        return $this->lookup;
    }

    /**
     * Sets the injection target specification.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface $injectionTarget The injection target specification
     *
     * @return void
     */
    public function setInjectionTarget(InjectionTargetDescriptorInterface $injectionTarget)
    {
        $this->injectionTarget = $injectionTarget;
    }

    /**
     * Returns the injection target specification.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface The injection target specification
     */
    public function getInjectionTarget()
    {
        return $this->injectionTarget;
    }

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new EpbReferenceDescriptor();
    }

    /**
     * Creates and initializes a beans reference configuration instance from the passed
     * reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the beans reference configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface|null The initialized descriptor instance
     *
     * @throws \Exception
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {
        throw new \Exception(__METHOD__ . ' not implemented yet');
    }

    /**
     * Creates and initializes a beans reference configuration instance from the passed
     * reflection property instance.
     *
     * @param \AppserverIo\Lang\Reflection\PropertyInterface $reflectionProperty The reflection property with the beans reference configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface|null The initialized descriptor instance
     */
    public function fromReflectionProperty(PropertyInterface $reflectionProperty)
    {

        // if we found a @EnterpriseBean annotation, load the annotation instance
        if ($reflectionProperty->hasAnnotation(EnterpriseBean::ANNOTATION) === false) {
            // if not, do nothing
            return;
        }

        // initialize the annotation instance
        $annotation = $reflectionProperty->getAnnotation(EnterpriseBean::ANNOTATION);

        // load the annotation instance
        $annotationInstance = $annotation->newInstance($annotation->getAnnotationName(), $annotation->getValues());

        // load the reference name defined as @EnterpriseBean(name=****)
        if ($name = $annotationInstance->getName()) {
            $this->setName(sprintf('%s/%s', EpbReferenceDescriptor::REF_DIRECTORY, $name));
        }

        // register the bean with the interface defined as @EnterpriseBean(beanInterface=****)
        if ($beanInterfaceAttribute = $annotationInstance->getBeanInterface()) {
            $this->setBeanInterface($beanInterfaceAttribute);
        } else {
            // use the property name as local business interface
            $this->setBeanInterface(sprintf('%sLocal', ucfirst($reflectionProperty->getPropertyName())));
        }

        // register the bean with the name defined as @EnterpriseBean(beanName=****)
        if ($beanNameAttribute = $annotationInstance->getBeanName()) {
            $this->setBeanName($beanNameAttribute);
        } else {
            // use the property name
            $this->setBeanName(ucfirst($reflectionProperty->getPropertyName()));
        }

        // register the bean with the lookup name defined as @EnterpriseBean(lookup=****)
        if ($lookupAttribute = $annotationInstance->getLookup()) {
            $this->setLookup($lookupAttribute);
        }

        // register the bean with the interface defined as @EnterpriseBean(description=****)
        if ($descriptionAttribute = $annotationInstance->getDescription()) {
            $this->setDescription($descriptionAttribute);
        }

        // load the injection target data
        $this->setInjectionTarget(InjectionTargetDescriptor::newDescriptorInstance()->fromReflectionProperty($reflectionProperty));

        // return the instance
        return $this;
    }

    /**
     * Creates and initializes a beans reference configuration instance from the passed
     * reflection method instance.
     *
     * @param \AppserverIo\Lang\Reflection\MethodInterface $reflectionMethod The reflection method with the beans reference configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface|null The initialized descriptor instance
     */
    public function fromReflectionMethod(MethodInterface $reflectionMethod)
    {

        // if we found a @EnterpriseBean annotation, load the annotation instance
        if ($reflectionMethod->hasAnnotation(EnterpriseBean::ANNOTATION) === false) {
            // if not, do nothing
            return;
        }

        // initialize the annotation instance
        $annotation = $reflectionMethod->getAnnotation(EnterpriseBean::ANNOTATION);

        // load the annotation instance
        $annotationInstance = $annotation->newInstance($annotation->getAnnotationName(), $annotation->getValues());

        // load the reference name defined as @EnterpriseBean(name=****)
        if ($name = $annotationInstance->getName()) {
            $this->setName(sprintf('%s/%s', EpbReferenceDescriptor::REF_DIRECTORY, $name));
        }

        // register the bean with the interface defined as @EnterpriseBean(beanInterface=****)
        if ($beanInterfaceAttribute = $annotationInstance->getBeanInterface()) {
            $this->setBeanInterface($beanInterfaceAttribute);
        } else {
            // use the name of the first parameter as local business interface
            foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                $this->setBeanInterface(sprintf('%sLocal', ucfirst($reflectionParameter->getParameterName())));
                break;
            }
        }

        // register the bean with the name defined as @EnterpriseBean(beanName=****)
        if ($beanNameAttribute = $annotationInstance->getBeanName()) {
            $this->setBeanName($beanNameAttribute);
        } else {
            // use the name of the first parameter as local business interface
            foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                $this->setBeanInterface(ucfirst($reflectionParameter->getParameterName()));
                break;
            }
        }

        // register the bean with the lookup name defined as @EnterpriseBean(lookup=****)
        if ($lookupAttribute = $annotationInstance->getLookup()) {
            $this->setLookup($lookupAttribute);
        }

        // register the bean with the interface defined as @EnterpriseBean(description=****)
        if ($descriptionAttribute = $annotationInstance->getDescription()) {
            $this->setDescription($descriptionAttribute);
        }

        // load the injection target data
        $this->setInjectionTarget(InjectionTargetDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod));

        // return the instance
        return $this;
    }

    /**
     * Creates and initializes a beans reference configuration instance from the passed
     * deployment node.
     *
     * @param \SimpleXmlElement $node The deployment node with the beans reference configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface|null The initialized descriptor instance
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // query if we've a <epb-ref> descriptor node
        if ($node->getName() !== 'epb-ref') {
            // if not, do nothing
            return;
        }

        // query for the reference name
        if ($name = (string) $node->{'epb-ref-name'}) {
            $this->setName(sprintf('%s/%s', EpbReferenceDescriptor::REF_DIRECTORY, $name));
        }

        // query for the bean name and set it
        if ($beanName = (string) $node->{'epb-link'}) {
            $this->setBeanName($beanName);
        }

        // query for the lookup name and set it
        if ($lookup = (string) $node->{'lookup-name'}) {
            $this->setLookup($lookup);
        }

        // query for the bean interface and set it
        if ($beanInterface = (string) $node->{'local'}) {
            $this->setBeanInterface($beanInterface);
        } elseif ($beanInterface = (string) $node->{'remote'}) {
            $this->setBeanInterface($beanInterface);
        } else {
            // use the bean name as local interface
            $this->setBeanInterface(sprintf('%sLocal', str_replace('Bean', '', $this->getBeanName())));
        }

        // query for the description and set it
        if ($description = (string) $node->{'description'}) {
            $this->setDescription($description);
        }

        // query for the injection target
        if ($injectionTarget = $node->{'injection-target'}) {
            $this->setInjectionTarget(InjectionTargetDescriptor::newDescriptorInstance()->fromDeploymentDescriptor($injectionTarget));
        }

        // return the instance
        return $this;
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface $epbReferenceDescriptor The configuration to merge
     *
     * @return void
     */
    public function merge(EpbReferenceDescriptorInterface $epbReferenceDescriptor)
    {

        // merge the reference name
        if ($name = $epbReferenceDescriptor->getName()) {
            $this->setName($name);
        }

        // merge the bean interface
        if ($beanInterface = $epbReferenceDescriptor->getBeanInterface()) {
            $this->setBeanInterface($beanInterface);
        }

        // merge the bean name
        if ($beanName = $epbReferenceDescriptor->getBeanName()) {
            $this->setBeanName($beanName);
        }

        // merge the lookup name
        if ($lookup = $epbReferenceDescriptor->getLookup()) {
            $this->setLookup($lookup);
        }

        // merge the description
        if ($description = $epbReferenceDescriptor->getDescription()) {
            $this->setDescription($description);
        }

        // merge the injection target
        if ($injectionTarget = $epbReferenceDescriptor->getInjectionTarget()) {
            $this->setInjectionTarget($injectionTarget);
        }
    }
}
