<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\SingletonSessionBeanDescriptor
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

namespace AppserverIo\Appserver\DependencyInjectionContainer\Description;

use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Startup;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\SingletonSessionBeanDescriptorInterface;

/**
 * Implementation for a singleton session bean descriptor.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class SingletonSessionBeanDescriptor extends SessionBeanDescriptor implements SingletonSessionBeanDescriptorInterface
{

    /**
     * Defines a keyword for a singleton session bean in a deployment descriptor node.
     *
     * @var string
     */
    const SESSION_TYPE = 'Singleton';

    /**
     * Whether the bean should be initialized on server startup.
     *
     * @var boolean
     */
    protected $initOnStartup = false;

    /**
     * Initialize the session bean descriptor with the session type.
     */
    public function __construct()
    {
        $this->setSessionType(SingletonSessionBeanDescriptor::SESSION_TYPE);
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
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\SingletonSessionBeanDescriptorInterface
     *     The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new SingletonSessionBeanDescriptor();
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
        return $reflectionClass->getAnnotation(Singleton::ANNOTATION);
    }

    /**
     * Initializes the bean descriptor instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\SingletonSessionBeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // query if we've an enterprise bean with a @Singleton annotation
        if ($reflectionClass->hasAnnotation(Singleton::ANNOTATION) === false) { // if not, do nothing
            return;
        }

        // initialize the descriptor instance
        parent::fromReflectionClass($reflectionClass);

        // if we found a bean with @Singleton + @Startup annotation
        if ($reflectionClass->hasAnnotation(Startup::ANNOTATION)) { // instanciate the bean
            $this->setInitOnStartup();
        }

        // return the instance
        return $this;
    }

    /**
     * Initializes a bean descriptor instance from the passed deployment descriptor node.
     *
     * @param \SimpleXmlElement $node The deployment node with the bean configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\SingletonSessionBeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // query if we've a <session> descriptor node
        if ($node->getName() !== 'session') { // if not, do nothing
            return;
        }

        // query if the session type matches
        if ((string) $node->{'session-type'} !== SingletonSessionBeanDescriptor::SESSION_TYPE) { // if not, do nothing
            return;
        }

        // initialize the descriptor instance
        parent::fromDeploymentDescriptor($node);

        // query for the startup flag
        if ($initOnStartup = (string) $node->{'init-on-startup'}) {
            $this->setInitOnStartup(Boolean::valueOf(new String($initOnStartup))->booleanValue());
        }

        // return the instance
        return $this;
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

        // merge the startup flag
        $this->setInitOnStartup($beanDescriptor->isInitOnStartup());
    }
}
