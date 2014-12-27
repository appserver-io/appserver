<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\StatefulSessionBeanDescriptor
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

use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateful;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface;

/**
 * Implementation for a stateful session bean descriptor.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class StatefulSessionBeanDescriptor extends SessionBeanDescriptor implements StatefulSessionBeanDescriptorInterface
{

    /**
     * Defines a keyword for a statefule session bean in a deployment descriptor node.
     *
     * @var string
     */
    const SESSION_TYPE = 'Stateful';

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface
     *     The descriptor instance
     */
    protected function newDescriptorInstance()
    {
        return new StatefulSessionBeanDescriptor();
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
        return $reflectionClass->getAnnotation(Stateful::ANNOTATION);
    }

    /**
     * Creates and initializes a bean configuration instance from the passed
     * deployment node.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface|null
     *     The initialized bean configuration
     */
    public static function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // query if we've an enterprise bean with a @Stateful annotation
        if ($reflectionClass->hasAnnotation(Stateful::ANNOTATION) === false) { // if not, do nothing
            return;
        }

        // create, initialize and return the new descriptor instance
        return parent::fromReflectionClass($reflectionClass);
    }

    /**
     * Creates and initializes a bean configuration instance from the passed
     * deployment node.
     *
     * @param \SimpleXmlElement $node The deployment node with the bean configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface|null
     *     The initialized bean configuration
     */
    public static function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // query if we've a <session> descriptor node
        if ($node->getName() !== 'session') { // if not, do nothing
            return;
        }

        // query if the session type matches
        if ($node->{'session-type'} !== StatefulSessionBeanDescriptor::SESSION_TYPE) { // if not, do nothing
            return;
        }

        // create, initialize and return the new descriptor instance
        return parent::fromDeploymentDescriptor($node);
    }
}
