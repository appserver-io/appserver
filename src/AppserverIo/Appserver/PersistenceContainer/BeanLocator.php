<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\BeanLocator
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
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\RemoteMethodInvocation\RemoteMethod;
use AppserverIo\Psr\EnterpriseBeans\BeanContext;
use AppserverIo\Psr\EnterpriseBeans\ResourceLocator;
use AppserverIo\Psr\EnterpriseBeans\InvalidBeanTypeException;
use AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateful;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateless;
use AppserverIo\Psr\EnterpriseBeans\Annotations\MessageDriven;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PostConstruct;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatefulSessionBeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\SingletonSessionBeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\StatelessSessionBeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\MessageDrivenBeanDescriptorInterface;

/**
 * The bean resource locator implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class BeanLocator implements ResourceLocator
{

    /**
     * Runs a lookup for the session bean with the passed class name and
     * session ID.
     *
     * If the passed class name is a session bean an instance
     * will be returned.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\BeanManager $beanManager The bean manager instance
     * @param string                                                  $className   The name of the session bean's class
     * @param string                                                  $sessionId   The session ID
     * @param array                                                   $args        The arguments passed to the session beans constructor
     *
     * @return object The requested session bean
     * @throws \AppserverIo\Psr\EnterpriseBeans\InvalidBeanTypeException Is thrown if passed class name is no session bean or is a entity bean (not implmented yet)
     */
    public function lookup(BeanContext $beanManager, $className, $sessionId = null, array $args = array())
    {

        // load the object manager
        $objectManager = $beanManager->getApplication()->search('ObjectManagerInterface');

        // load the bean descriptor
        $descriptor = $objectManager->getObjectDescriptors()->get($className);

        // query if we've a Stateful session bean
        if ($descriptor instanceof StatefulSessionBeanDescriptorInterface) {
            // try to load the stateful session bean from the bean manager
            if ($instance = $beanManager->lookupStatefulSessionBean($sessionId, $className)) {
                return $instance;
            }

            // if not create a new instance and return it
            $instance = $beanManager->newInstance($className, $sessionId, $args);

            // we've to check for post-construct callback
            foreach ($descriptor->getPostConstructCallbacks() as $postConstructCallback) {
                $instance->$postConstructCallback();
            }

            // return the instance
            return $instance;
        }

        // query if we've a Singleton session bean
        if ($descriptor instanceof SingletonSessionBeanDescriptorInterface) {
            // try to load the singleton session bean from the bean manager
            if ($instance = $beanManager->lookupSingletonSessionBean($className)) {
                return $instance;
            }

            // singleton session beans MUST extends \Stackable
            if (is_subclass_of($className, '\Stackable') === false) {
                throw new EnterpriseBeansException(sprintf('Singleton session bean %s MUST extend \Stackable', $className));
            }

            // if not create a new instance and return it
            $instance = $beanManager->newInstance($className, $sessionId, $args);

            // add the singleton session bean to the container
            $beanManager->getSingletonSessionBeans()->set($className, $instance);

            // we've to check for post-construct callback
            foreach ($descriptor->getPostConstructCallbacks() as $postConstructCallback) {
                $instance->$postConstructCallback();
            }

            // return the instance
            return $instance;
        }

        // query if we've a Stateless session bean
        if ($descriptor instanceof StatelessSessionBeanDescriptorInterface) {
            // if not create a new instance and return it
            $instance = $beanManager->newInstance($className, $sessionId, $args);

            // we've to check for post-construct callback
            foreach ($descriptor->getPostConstructCallbacks() as $postConstructCallback) {
                $instance->$postConstructCallback();
            }

            // return the instance
            return $instance;
        }

        //  query if we've a MessageDriven bean
        if ($descriptor instanceof MessageDrivenBeanDescriptorInterface) {
            // create a new instance and return it
            return $beanManager->newInstance($className, $sessionId, $args);
        }

        // we've an unknown bean type => throw an exception
        throw new InvalidBeanTypeException(sprintf('Try to lookup a bean %s with missing enterprise annotation', $className));
    }
}
