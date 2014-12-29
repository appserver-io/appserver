<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfiguration
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
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Utils;

use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\MessageDriven;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PreDestroy;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PostConstruct;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Startup;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateful;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateless;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Schedule;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Timeout;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Resource;
use AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException;

/**
 * Utility class with some bean utilities.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class BeanConfiguration implements BeanConfigurationInterface
{

    /**
     * The bean name.
     *
     * @var string
     */
    protected $name;

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
     * The beans session type.
     *
     * @var string
     */
    protected $sessionType = MessageDriven::ANNOTATION;

    /**
     * The beans description.
     *
     * @var string
     */
    protected $description;

    /**
     * Whether the bean should be initialized on server startup.
     *
     * @var boolean
     */
    protected $initOnStartup = false;

    /**
     * The array with the post construct callback method names.
     *
     * @var array
     */
    protected $postConstructCallbacks = array();

    /**
     * The array with the pre destroy callback method names.
     *
     * @var array
     */
    protected $preDestroyCallbacks = array();

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
     * @param string $name The beans class name
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
     * @param string $name The configurable bean name
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
     * @param string $name The bean interface
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
     * Sets the beans session type.
     *
     * @param string $sessionType The beans session type
     *
     * @return void
     */
    public function setSessionType($sessionType)
    {
        $this->sessionType = $sessionType;
    }

    /**
     * Returns the beans session type.
     *
     * @return string The beans session type
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

    /**
     * Sets the flag whether the bean should be initialized on startup or not.
     *
     * @param boolean $initOnStartup TRUE if the bean should be initialized on startup, else FALSE
     *
     * @return void
     */
    public function setInitOnStartup($initOnStartup = true)
    {
        $this->initOnStartup = $initOnStartup;
    }

    /**
     * Queries whether the bean should be initialized on startup or not.
     *
     * @return boolean TRUE if the bean should be initialized on startup, else FALSE
     */
    public function isInitOnStartup()
    {
        return $this->initOnStartup;
    }

    /**
     * Adds a post construct callback method name.
     *
     * @param string $postConstructCallback The post construct callback method name
     *
     * @return void
     */
    public function addPostConstructCallback($postConstructCallback)
    {
        $this->postConstructCallbacks[] = $postConstructCallback;
    }

    /**
     * Adds a post construct callback method name.
     *
     * @param string $postConstructCallback The post construct callback method name
     *
     * @return void
     */
    public function addPreDestroyCallback($preDestroyCallback)
    {
        $this->preDestroyCallbacks[] = $preDestroyCallback;
    }

    /**
     * Adds a EPB reference configuration.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\Utils\EpbReferenceInterface $epbReference The EPB reference configuration
     *
     * @return void
     */
    public function addEpbReference(EpbReferenceInterface $epbReference)
    {
        $this->epbReferences[$epbReference->getRefName()] = $epbReference;
    }

    /**
     * Sets the array with the post construct callback method names.
     *
     * @param array $postConstructCallbacks The post construct callback method names
     *
     * @return void
     */
    public function setPostConstructCallbacks(array $postConstructCallbacks)
    {
        $this->postConstructCallbacks = $postConstructCallbacks;
    }

    /**
     * The array with the post construct callback method names.
     *
     * @return array The post construct callback method names
     */
    public function getPostConstructCallbacks()
    {
        return $this->postConstructCallbacks;
    }

    /**
     * Sets the array with the pre destroy callback method names.
     *
     * @param array $preDestroyCallbacks The pre destroy callback method names
     *
     * @return void
     */
    public function setPreDestroyCallbacks(array $preDestroyCallbacks)
    {
        $this->preDestroyCallbacks = $preDestroyCallbacks;
    }

    /**
     * The array with the pre destroy callback method names.
     *
     * @return array The pre destroy callback method names
     */
    public function getPreDestroyCallbacks()
    {
        return $this->preDestroyCallbacks;
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
     * Creates and initializes a bean configuration instance from the passed
     * deployment node.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfiguration The initialized bean configuration
     */
    public static function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // create a new configuration instance
        $configuration = new BeanConfiguration();

        // declare the local variable for the reflection annotation instance
        $reflectionAnnotation = null;

        // if we found an enterprise bean with either a @Singleton annotation
        if ($reflectionClass->hasAnnotation(Singleton::ANNOTATION)) {
            $reflectionAnnotation = $reflectionClass->getAnnotation(Singleton::ANNOTATION);
        }

        // if we found an enterprise bean with either a @Stateless annotation
        if ($reflectionClass->hasAnnotation(Stateless::ANNOTATION)) {
            $reflectionAnnotation = $reflectionClass->getAnnotation(Stateless::ANNOTATION);
        }

        // if we found an enterprise bean with either a @Stateful annotation
        if ($reflectionClass->hasAnnotation(Stateful::ANNOTATION)) {
            $reflectionAnnotation = $reflectionClass->getAnnotation(Stateful::ANNOTATION);
        }

        // if we found an enterprise bean with either a @MessageDriven annotation
        if ($reflectionClass->hasAnnotation(MessageDriven::ANNOTATION)) {
            $reflectionAnnotation = $reflectionClass->getAnnotation(MessageDriven::ANNOTATION);
        }

        // load class name
        $configuration->setClassName($reflectionClass->getName());

        // can't register the bean, because of a missing enterprise bean annotation
        if ($reflectionAnnotation == null) {
            return $configuration;
        }

        // we've to check for a @PostConstruct or @PreDestroy annotation
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {

            // if we found a @PostConstruct annotation, invoke the method
            if ($reflectionMethod->hasAnnotation(PostConstruct::ANNOTATION)) {
                $configuration->addPostConstructCallback($reflectionMethod->getMethodName());
            }

            // if we found a @PreDestroy annotation, invoke the method
            if ($reflectionMethod->hasAnnotation(PreDestroy::ANNOTATION)) {
                $configuration->addPreDestroyCallback($reflectionMethod->getMethodName());
            }
        }

        // initialize the annotation instance
        $annotationInstance = $reflectionAnnotation->newInstance(
            $reflectionAnnotation->getAnnotationName(),
            $reflectionAnnotation->getValues()
        );

        // load the default name to register in naming directory
        if ($nameAttribute = $annotationInstance->getName()) {
            $configuration->setName($nameAttribute);
        } else { // if @Annotation(name=****) is NOT set, we use the short class name by default
            $configuration->setName($reflectionClass->getShortName());
        }

        // register the bean with the interface defined as @Annotation(beanInterface=****)
        if ($beanInterfaceAttribute = $annotationInstance->getBeanInterface()) {
            $configuration->setBeanInterface($beanInterfaceAttribute);
        }

        // register the bean with the name defined as @Annotation(beanName=****)
        if ($beanNameAttribute = $annotationInstance->getBeanName()) {
            $configuration->setBeanName($beanNameAttribute);
        }

        // register the bean with the name defined as @Annotation(mappedName=****)
        if ($mappedNameAttribute = $annotationInstance->getMappedName()) {
            $configuration->setMappedName($mappedNameAttribute);
        }

        // if we found a bean with @Singleton + @Startup annotation
        if ($reflectionClass->hasAnnotation(Singleton::ANNOTATION) &&
            $reflectionClass->hasAnnotation(Startup::ANNOTATION)) { // instanciate the bean
            $configuration->setInitOnStartup();
        }

        // return the initialized configuration
        return $configuration;
    }

    /**
     * Creates and initializes a bean configuration instance from the passed
     * deployment node.
     *
     * @param \SimpleXmlElement $node The deployment node with the bean configuration
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Utils\BeanConfiguration The initialized bean configuration
     */
    public static function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // create a new configuration instance
        $configuration = new BeanConfiguration();

        // query for the class name and set it
        if ($className = (string) $node->{'epb-class'}) {
            $configuration->setClassName($className);
        }

        // query for the name and set it
        if ($name = (string) $node->{'epb-name'}) {
            $configuration->setName($name);
        }

        // query for the bean interface and set it
        if ($beanInterface = (string) $node->{'bean-interface'}) {
            $configuration->setBeanInterface($beanInterface);
        }

        // query for the bean name and set it
        if ($beanName = (string) $node->{'bean-name'}) {
            $configuration->setBeanName($beanName);
        }

        // query for the mapped name and set it
        if ($mappedName = (string) $node->{'mapped-name'}) {
            $configuration->setMappedName($mappedName);
        }

        // query for the description and set it
        if ($description = (string) $node->{'description'}) {
            $configuration->setDescription($description);
        }

        // query for the session type and set it
        if ($sessionType = (string) $node->{'session-type'}) {
            $configuration->setSessionType($sessionType);
        }

        // query for the startup flag
        if ($initOnStartup = (string) $node->{'init-on-startup'}) {
            $configuration->setInitOnStartup(Boolean::valueOf(new String($initOnStartup))->booleanValue());
        }

        // initialize the post construct callback methods
        foreach ($node->xpath('post-construct/lifecycle-callback-method') as $postConstructCallback) {
            $configuration->addPostConstructCallback((string) $postConstructCallback);
        }

        // initialize the pre destroy callback methods
        foreach ($node->xpath('pre-destroy/lifecycle-callback-method') as $preDestroyCallback) {
            $configuration->addPreDestroyCallback((string) $preDestroyCallback);
        }

        // initialize the enterprise bean references
        foreach ($node->xpath('epb-ref') as $epbReference) {
            $configuration->addEpbReference(EpbReference::fromDeploymentDescriptor($epbReference));
        }

        // return the initialized configuration
        return $configuration;
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\UtilsBeanConfigurationInterface $configuration The configuration to merge
     *
     * @return void
     */
    public function merge(BeanConfigurationInterface $configuration)
    {

        // check if the classes are equal
        if ($this->getClassName() !== $configuration->getClassName()) {
            throw new EnterpriseBeansException(
                sprintf('You try to merge a bean configuration for % with %s', $configuration->getClassName(), $this->getClassName())
            );
        }

        // merge the name
        if ($name = $configuration->getName()) {
            $this->setName($name);
        }

        // merge the bean interface
        if ($beanInterface = $configuration->getBeanInterface()) {
            $this->setBeanInterface($beanInterface);
        }

        // merge the bean name
        if ($beanName = $configuration->getBeanName()) {
            $this->setBeanName($beanName);
        }

        // merge the description
        if ($description = $configuration->getDescription()) {
            $this->setDescription($description);
        }

        // merge the session type
        if ($sessionType = $configuration->getSessionType()) {
            $this->setSessionType($sessionType);
        }

        // merge the startup flag
        $this->setInitOnStartup($configuration->isInitOnStartup());

        // merge the post construct callback method names
        foreach ($configuration->getPostConstructCallbacks() as $postConstructCallback) {
            if (in_array($postConstructCallback, $this->postConstructCallbacks) === false) {
                $this->addPostConstructCallback($postConstructCallback);
            }
        }

        // merge the pre destroy callback method names
        foreach ($configuration->getPreDestroyCallbacks() as $preDestroyCallback) {
            if (in_array($preDestroyCallback, $this->preDestroyCallbacks) === false) {
                $this->addPreDestroyCallback($preDestroyCallback);
            }
        }

        // mrege the EPB references
        foreach ($configuration->getEpbReferences() as $epbReference) {
            if (isset($this->epbReferences[$epbReference->getRefName()]) === false) {
                $this->addEpbReference($epbReference);
            }
        }
    }
}
