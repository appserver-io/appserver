<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\StatelessSessionBeanDescriptor
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
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateless;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatelessSessionBeanDescriptorInterface;

/**
 * Implementation for a stateless session bean descriptor.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage DependencyInjectionContainer
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class StatelessSessionBeanDescriptor extends SessionBeanDescriptor implements StatelessSessionBeanDescriptorInterface
{

    /**
     * Defines a keyword for a stateless session bean in a deployment descriptor node.
     *
     * @var string
     */
    const SESSION_TYPE = 'Stateless';

    /**
     * Initialize the session bean descriptor with the session type.
     */
    public function __construct()
    {
        $this->setSessionType(StatelessSessionBeanDescriptor::SESSION_TYPE);
    }

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatelessSessionBeanDescriptorInterface
     *     The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new StatelessSessionBeanDescriptor();
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
        return $reflectionClass->getAnnotation(Stateless::ANNOTATION);
    }

    /**
     * Initializes the bean descriptor instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatelessSessionBeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // query if we've an enterprise bean with a @Stateless annotation
        if ($reflectionClass->hasAnnotation(Stateless::ANNOTATION) === false) { // if not, do nothing
            return;
        }

        // set the session type
        $this->setSessionType(StatelessSessionBeanDescriptor::SESSION_TYPE);

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
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatelessSessionBeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // query if we've a <session> descriptor node
        if ($node->getName() !== 'session') { // if not, do nothing
            return;
        }

        // query if the session type matches
        if ((string) $node->{'session-type'} !== StatelessSessionBeanDescriptor::SESSION_TYPE) { // if not, do nothing
            return;
        }

        // initialize the descriptor instance
        parent::fromDeploymentDescriptor($node);

        // return the instance
        return $this;
    }
}
