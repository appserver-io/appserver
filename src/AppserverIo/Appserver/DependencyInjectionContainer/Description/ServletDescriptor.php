<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\ServletDescriptor
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

use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\Annotations\Route;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ServletDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ResReferenceDescriptorInterface;

/**
 * A servlet descriptor implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServletDescriptor implements ServletDescriptorInterface
{

    /**
     * The servlet name.
     *
     * @var string
     */
    protected $name;

    /**
     * The servlets class name.
     *
     * @var string
     */
    protected $className;

    /**
     * The servlets description.
     *
     * @var string
     */
    protected $description;

    /**
     * The servlets display name.
     *
     * @var string
     */
    protected $displayName;

    /**
     * The array with the initialization parameters.
     *
     * @var array
     */
    protected $initParams = array();

    /**
     * The array with the URL patterns.
     *
     * @var array
     */
    protected $urlPatterns = array();

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
     * Sets the servlet name.
     *
     * @param string $name The servlet name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the servlet name.
     *
     * @return string The servlet name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the servlets class name.
     *
     * @param string $className The servlets class name
     *
     * @return void
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Returns the servlets class name.
     *
     * @return string The servlets class name
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Sets the servlets description.
     *
     * @param string $description The servlets description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the servlets description.
     *
     * @return string The servlets description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the servlets display name.
     *
     * @param string $displayName The servlets display name
     *
     * @return void
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Returns the servlets display name.
     *
     * @return string The servlets display name
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Adds a initialization parameter key/value pair.
     *
     * @param string $key   The key of the initialization parameter
     * @param string $value The value of the initialization parameter
     *
     * @return void
     */
    public function addInitParam($key, $value)
    {
        $this->initParams[$key] = $value;
    }

    /**
     * Sets the array with the initialization parameters.
     *
     * @param array $initParams The initialization parameters
     *
     * @return void
     */
    public function setInitParams(array $initParams)
    {
        $this->initParams = $initParams;
    }

    /**
     * The array with the initialization parameters.
     *
     * @return array The initialization parameters
     */
    public function getInitParams()
    {
        return $this->initParams;
    }

    /**
     * Adds a URL pattern.
     *
     * @param string $urlPattern The URL pattern
     *
     * @return void
     */
    public function addUrlPattern($urlPattern)
    {
        if (in_array($urlPattern, $this->urlPatterns) === false) {
            $this->urlPatterns[] = $urlPattern;
        }
    }

    /**
     * Sets the array with the URL patterns.
     *
     * @param array $urlPatterns The URL patterns
     *
     * @return void
     */
    public function setUrlPatterns(array $urlPatterns)
    {
        $this->urlPatterns = $urlPatterns;
    }

    /**
     * The array with the URL patterns.
     *
     * @return array The URL patterns
     */
    public function getUrlPatterns()
    {
        return $this->urlPatterns;
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
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new ServletDescriptor();
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
        return $reflectionClass->getAnnotation(Route::ANNOTATION);
    }

    /**
     * Initializes the servlet descriptor instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the servlet description
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ServletDescriptorInterface|null The initialized descriptor instance
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // query if we've a servlet
        if ($reflectionClass->implementsInterface('AppserverIo\Psr\Servlet\ServletInterface') === false) {
            // if not, do nothing
            return;
        }

        // query if we've an interface or an abstract class
        if ($reflectionClass->toPhpReflectionClass()->isInterface() ||
            $reflectionClass->toPhpReflectionClass()->isAbstract()) {
            // if so, do nothing
            return;
        }

        // set the servlet name
        $this->setName(lcfirst($reflectionClass->getShortName()));

        // set the class name
        $this->setClassName($reflectionClass->getName());

        // query if we've a servlet with a @Route annotation
        if ($reflectionClass->hasAnnotation(Route::ANNOTATION)) {
            // create a new annotation instance
            $reflectionAnnotation = $this->newAnnotationInstance($reflectionClass);

            // initialize the annotation instance
            $annotationInstance = $reflectionAnnotation->newInstance(
                $reflectionAnnotation->getAnnotationName(),
                $reflectionAnnotation->getValues()
            );

            // load the default name to register in naming directory
            if ($nameAttribute = $annotationInstance->getName()) {
                $this->setName($nameAttribute);
            }

            // register the servlet description defined as @Route(description=****)
            if ($description = $annotationInstance->getDescription()) {
                $this->setDescription($description);
            }

            // register the servlet display name defined as @Route(displayName=****)
            if ($displayName = $annotationInstance->getDisplayName()) {
                $this->setDisplayName($displayName);
            }

            // register the init params defined as @Route(initParams=****)
            foreach ($annotationInstance->getInitParams() as $initParam) {
                list ($paramName, $paramValue) = $initParam;
                $this->addInitParam($paramName, $paramValue);
            }

            // register the URL pattern defined as @Route(urlPattern=****)
            foreach ($annotationInstance->getUrlPattern() as $urlPattern) {
                $this->addUrlPattern($urlPattern);
            }
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

        // return the instance
        return $this;
    }

    /**
     * Initializes a servlet descriptor instance from the passed deployment descriptor node.
     *
     * @param \SimpleXmlElement $node The deployment node with the servlet description
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ServletDescriptorInterface|null The initialized descriptor instance
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {
        $node->registerXPathNamespace('a', 'http://www.appserver.io/appserver');

        // query if we've a <servlet> descriptor node
        if ($node->getName() !== 'servlet') {
            // if not, do nothing
            return;
        }

        // query for the class name and set it
        if ($className = (string) $node->{'servlet-class'}) {
            $this->setClassName($className);
        }

        // query for the name and set it
        if ($name = (string) $node->{'servlet-name'}) {
            $this->setName($name);
        }

        // query for the description and set it
        if ($description = (string) $node->{'description'}) {
            $this->setDescription($description);
        }

        // query for the display name and set it
        if ($displayName = (string) $node->{'display-name'}) {
            $this->setDescription($displayName);
        }

        // append the init params to the servlet configuration
        foreach ($node->{'init-param'} as $initParam) {
            $this->addInitParam((string) $initParam->{'param-name'}, (string) $initParam->{'param-value'});
        }

        // initialize the EPB references
        foreach ($node->xpath('a:epb-ref') as $epbReference) {
            $this->addEpbReference(EpbReferenceDescriptor::newDescriptorInstance()->fromDeploymentDescriptor($epbReference));
        }

        // initialize the resource references
        foreach ($node->xpath('a:res-ref') as $resReference) {
            $this->addResReference(ResReferenceDescriptor::newDescriptorInstance()->fromDeploymentDescriptor($resReference));
        }

        // return the instance
        return $this;
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ServletDescriptorInterface $servletDescriptor The descriptor to merge
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if you try to merge a servlet descriptor with a different class name
     */
    public function merge(ServletDescriptorInterface $servletDescriptor)
    {

        // check if the classes are equal
        if ($this->getClassName() !== $servletDescriptor->getClassName()) {
            throw new ServletException(
                sprintf('You try to merge a servlet descriptor for % with %s', $servletDescriptor->getClassName(), $servletDescriptor->getClassName())
            );
        }

        // merge the servlet name
        if ($name = $servletDescriptor->getName()) {
            $this->setName($name);
        }

        // merge the servlet description
        if ($description = $servletDescriptor->getDescription()) {
            $this->setDescription($description);
        }

        // merge the servlet display name
        if ($displayName = $servletDescriptor->getDisplayName()) {
            $this->setDisplayName($displayName);
        }

        // merge the EPB references
        foreach ($servletDescriptor->getEpbReferences() as $epbReference) {
            $this->addEpbReference($epbReference);
        }

        // merge the EPB references
        foreach ($servletDescriptor->getResReferences() as $resReference) {
            $this->addResReference($resReference);
        }
    }
}
