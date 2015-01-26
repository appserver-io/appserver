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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer\Description;

use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateful;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface;

/**
 * Implementation for a stateful session bean descriptor.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
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
     * Initialize the session bean descriptor with the session type.
     */
    public function __construct()
    {
        $this->setSessionType(StatefulSessionBeanDescriptor::SESSION_TYPE);
    }

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface
     *     The descriptor instance
     */
    public static function newDescriptorInstance()
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
     * Initializes the bean descriptor instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // query if we've an enterprise bean with a @Stateful annotation
        if ($reflectionClass->hasAnnotation(Stateful::ANNOTATION) === false) {
            // if not, do nothing
            return;
        }

        // set the session type
        $this->setSessionType(StatefulSessionBeanDescriptor::SESSION_TYPE);

        // initialize the descriptor instance
        parent::fromReflectionClass($reflectionClass);

        // return the instance
        return $this;
    }

    /**
     * Initializes a bean descriptor instance from the passed deployment descriptor node.
     *
     * @param \SimpleXmlElement $node The deployment node with the bean configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // query if we've a <session> descriptor node
        if ($node->getName() !== 'session') {
            // if not, do nothing
            return;
        }

        // query if the session type matches
        if ((string) $node->{'session-type'} !== StatefulSessionBeanDescriptor::SESSION_TYPE) {
            // if not, do nothing
            return;
        }

        // initialize the descriptor instance
        parent::fromDeploymentDescriptor($node);

        // return the instance
        return $this;
    }
}
