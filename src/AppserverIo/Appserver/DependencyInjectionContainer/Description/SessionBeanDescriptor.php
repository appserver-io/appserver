<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\SessionBeanDescriptor
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

use AppserverIo\Lang\String;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PreDestroy;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PostConstruct;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\SessionBeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface;

/**
 * Implementation for an abstract session bean descriptor.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage DependencyInjectionContainer
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class SessionBeanDescriptor extends BeanDescriptor implements SessionBeanDescriptorInterface
{

    /**
     * The beans session type.
     *
     * @var string
     */
    protected $sessionType;

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
     * Adds a pre destroy callback method name.
     *
     * @param string $preDestroyCallback The pre destroy callback method name
     *
     * @return void
     */
    public function addPreDestroyCallback($preDestroyCallback)
    {
        $this->preDestroyCallbacks[] = $preDestroyCallback;
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
     * Initializes the bean descriptor instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean description
     *
     * @return void
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // initialize the bean descriptor
        parent::fromReflectionClass($reflectionClass);

        // we've to check for a @PostConstruct or @PreDestroy annotation
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {

            // if we found a @PostConstruct annotation, invoke the method
            if ($reflectionMethod->hasAnnotation(PostConstruct::ANNOTATION)) {
                $this->addPostConstructCallback($reflectionMethod->getMethodName());
            }

            // if we found a @PreDestroy annotation, invoke the method
            if ($reflectionMethod->hasAnnotation(PreDestroy::ANNOTATION)) {
                $this->addPreDestroyCallback($reflectionMethod->getMethodName());
            }
        }
    }

    /**
     * Initializes a bean descriptor instance from the passed deployment descriptor node.
     *
     * @param \SimpleXmlElement $node The deployment node with the bean description
     *
     * @return void
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // initialize the bean descriptor
        parent::fromDeploymentDescriptor($node);

        // query for the session type and set it
        if ($sessionType = (string) $node->{'session-type'}) {
            $this->setSessionType($sessionType);
        }

        // initialize the post construct callback methods
        foreach ($node->xpath('post-construct/lifecycle-callback-method') as $postConstructCallback) {
            $this->addPostConstructCallback((string) $postConstructCallback);
        }

        // initialize the pre destroy callback methods
        foreach ($node->xpath('pre-destroy/lifecycle-callback-method') as $preDestroyCallback) {
            $this->addPreDestroyCallback((string) $preDestroyCallback);
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

        // merge the default bean members by invoking the parent method
        parent::merge($beanDescriptor);

        // merge the session type
        if ($sessionType = $beanDescriptor->getSessionType()) {
            $this->setSessionType($sessionType);
        }

        // merge the post construct callback method names
        foreach ($beanDescriptor->getPostConstructCallbacks() as $postConstructCallback) {
            if (in_array($postConstructCallback, $this->postConstructCallbacks) === false) {
                $this->addPostConstructCallback($postConstructCallback);
            }
        }

        // merge the pre destroy callback method names
        foreach ($beanDescriptor->getPreDestroyCallbacks() as $preDestroyCallback) {
            if (in_array($preDestroyCallback, $this->preDestroyCallbacks) === false) {
                $this->addPreDestroyCallback($preDestroyCallback);
            }
        }
    }
}
