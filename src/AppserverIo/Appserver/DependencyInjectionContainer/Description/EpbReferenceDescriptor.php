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
 * @category   Server
 * @package    Appserver
 * @subpackage DependencyInjectionContainer
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer\Description;

use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface;
use AppserverIo\Lang\Reflection\PropertyInterface;
use AppserverIo\Lang\Reflection\AnnotationInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Resource;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Lang\Reflection\MethodInterface;

/**
 * Utility class that stores a beans reference configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage DependencyInjectionContainer
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class EpbReferenceDescriptor implements EpbReferenceDescriptorInterface
{

    /**
     * Defines the value for the reference type 'Session' in a relation.
     *
     * @var string
     */
    const REF_TYPE_SESSION = 'Session';

    /**
     * Defines the value for the reference type 'Resource' in a relation.
     *
     * @var string
     */
    const REF_TYPE_RESOURCE = 'Resource';

    /**
     * The reference name.
     *
     * @var string
     */
    protected $refName;

    /**
     * The reference type.
     *
     * @var string
     */
    protected $refType;

    /**
     * The configurable bean name.
     *
     * @var string
     */
    protected $link;

    /**
     * The injection target specification.
     *
     * @var \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\InjectionTargetDescriptorInterface
     */
    protected $injectionTarget;

    /**
     * Sets the reference name.
     *
     * @param string $refName The reference name
     *
     * @return void
     */
    public function setRefName($refName)
    {
        $this->refName = $refName;
    }

    /**
     * Returns the reference name.
     *
     * @return string The reference name
     */
    public function getRefName()
    {
        return $this->refName;
    }

    /**
     * Sets the reference type.
     *
     * @param string $refType The reference type
     *
     * @return void
     */
    public function setRefType($refType)
    {
        $this->refType = $refType;
    }

    /**
     * Returns the reference type.
     *
     * @return string The reference type
     */
    public function getRefType()
    {
        return $this->refType;
    }

    /**
     * Sets the reference link.
     *
     * @param string $link The reference link
     *
     * @return void
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Returns the reference link.
     *
     * @return string The reference link
     */
    public function getLink()
    {
        return $this->link;
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
        if ($reflectionProperty->hasAnnotation(EnterpriseBean::ANNOTATION)) {

            // initialize the annotation instance
            $annotation = $reflectionProperty->getAnnotation(EnterpriseBean::ANNOTATION);

            // initialize the reference type
            $refType = EpbReferenceDescriptor::REF_TYPE_SESSION;
        }

        // if we found a @Resource annotation, load the annotation instance
        if ($reflectionProperty->hasAnnotation(Resource::ANNOTATION)) {

            // initialize the annotation instance
            $annotation = $reflectionProperty->getAnnotation(Resource::ANNOTATION);

            // initialize the reference type
            $refType = EpbReferenceDescriptor::REF_TYPE_RESOURCE;
        }

        // if we found a annotation instance, initialize the instance
        if ($annotation instanceof AnnotationInterface) {

            // load the annotation instance
            $annotationInstance = $annotation->newInstance($annotation->getAnnotationName(), $annotation->getValues());

            // load the reference name
            if ($refName = $annotationInstance->getName()) {
                $this->setRefName($refName);
            }

            // set the ref type
            $this->setRefType($refType);

            // load the injection target data
            $this->setInjectionTarget(InjectionTargetDescriptor::newDescriptorInstance()->fromReflectionProperty($reflectionProperty));

            // return the instance
            return $this;
        }
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
        if ($reflectionMethod->hasAnnotation(EnterpriseBean::ANNOTATION)) {

            // initialize the annotation instance
            $annotation = $reflectionMethod->getAnnotation(EnterpriseBean::ANNOTATION);

            // initialize the reference type
            $refType = EpbReferenceDescriptor::REF_TYPE_SESSION;
        }

        // if we found a @Resource annotation, load the annotation instance
        if ($reflectionMethod->hasAnnotation(Resource::ANNOTATION)) {

            // initialize the annotation instance
            $annotation = $reflectionMethod->getAnnotation(Resource::ANNOTATION);

            // initialize the reference type
            $refType = EpbReferenceDescriptor::REF_TYPE_RESOURCE;
        }

        // if we found a annotation instance, initialize the instance
        if ($annotation instanceof AnnotationInterface) {

            // load the annotation instance
            $annotationInstance = $annotation->newInstance($annotation->getAnnotationName(), $annotation->getValues());

            // load the reference name
            if ($refName = $annotationInstance->getName()) {
                $this->setRefName($refName);
            }

            // set the ref type
            $this->setRefType($refType);

            // load the injection target data
            $this->setInjectionTarget(InjectionTargetDescriptor::newDescriptorInstance()->fromReflectionMethod($reflectionMethod));

            // return the instance
            return $this;
        }
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

        // query for the reference name
        if ($refName = (string) $node->{'epb-ref-name'}) {
            $this->setRefName($refName);
        }

        // query for the reference type
        if ($refType = (string) $node->{'epb-ref-type'}) {
            $this->setRefType($refType);
        }

        // query for reference link
        if ($link = (string) $node->{'epb-link'}) {
            $this->setLink($link);
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
     * @param \AppserverIo\Appserver\PersistenceContainer\EbpReferenceDescriptorInterface $epbReferenceDescriptor The configuration to merge
     *
     * @return void
     */
    public function merge(EpbReferenceDescriptorInterface $epbReferenceDescriptor)
    {

        // merge the reference name
        if ($refName = $epbReferenceDescriptor->getRefName()) {
            $this->setRefName($refName);
        }

        // merge the reference type
        if ($refType = $epbReferenceDescriptor->getRefType()) {
            $this->setRefType($refType);
        }

        // merge the reference link
        if ($link = $epbReferenceDescriptor->getLink()) {
            $this->setLink($link);
        }

        // merge the injection target
        if ($injectionTarget = $epbReferenceDescriptor->getInjectionTarget()) {
            $this->setInjectionTarget($injectionTarget);
        }
    }
}
