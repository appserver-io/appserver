<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\MessageDrivenBeanDescriptor
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
use AppserverIo\Psr\EnterpriseBeans\Annotations\MessageDriven;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\MessageDrivenBeanDescriptorInterface;

/**
 * Implementation for message driven bean descriptor.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MessageDrivenBeanDescriptor extends BeanDescriptor implements MessageDrivenBeanDescriptorInterface
{

    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\MessageDrivenBeanDescriptorInterface
     *     The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new MessageDrivenBeanDescriptor();
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
        return $reflectionClass->getAnnotation(MessageDriven::ANNOTATION);
    }

    /**
     * Initializes the bean descriptor instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean configuration
     *
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\MesssageDrivenBeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // query if we've an enterprise bean with a @MessageDriven annotation
        if ($reflectionClass->hasAnnotation(MessageDriven::ANNOTATION) === false) {
            // if not, do nothing
            return;
        }

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
     * @return \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\MesssageDrivenBeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {

        // query if we've a <message-driven> descriptor node
        if ($node->getName() !== 'message-driven') {
            // if not, do nothing
            return;
        }

        // initialize the descriptor instance
        parent::fromDeploymentDescriptor($node);

        // return the instance
        return $this;
    }
}
