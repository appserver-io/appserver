<?php

/**
 * AppserverIo\Appserver\Core\AbstractManager
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

namespace AppserverIo\Appserver\Core;

use AppserverIo\Storage\StorageInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Naming\InitialContext;
use AppserverIo\Appserver\DependencyInjectionContainer\Description\EpbReferenceDescriptor;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ResReferenceDescriptorInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;

/**
 * Abstract manager implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
abstract class AbstractManager extends GenericStackable implements ManagerInterface
{

    /**
     * Inject the data storage.
     *
     * @param \AppserverIo\Storage\StorageInterface $data The data storage to use
     *
     * @return void
     */
    public function injectData(StorageInterface $data)
    {
        $this->data = $data;
    }

    /**
     * Inject the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * The global naming directory.
     *
     * @param \AppserverIo\Appserver\Naming\InitialContext $initialContext The global naming directory
     *
     * @return void
     */
    public function injectInitialContext(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Returns the global naming directory.
     *
     * @return \AppserverIo\Appserver\Naming\InitialContext The global naming directory
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the absolute path to the web application.
     *
     * @return string The absolute path
     */
    public function getWebappPath()
    {
        return $this->getApplication()->getWebappPath();
    }

    /**
     * Registers the value with the passed key in the container.
     *
     * @param string $key   The key to register the value with
     * @param object $value The value to register
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->data->set($key, $value);
    }

    /**
     * Returns the attribute with the passed key from the container.
     *
     * @param string $key The key the requested value is registered with
     *
     * @return mixed|null The requested value if available
     */
    public function getAttribute($key)
    {
        if ($this->data->has($key)) {
            return $this->data->get($key);
        }
    }

    /**
     * Returns a new reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     */
    public function newReflectionClass($className)
    {
        return $this->getApplication()->search('ProviderInterface')->newReflectionClass($className);
    }

    /**
     * Registers the passed EPB reference in the applications directory.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\EpbReferenceDescriptorInterface $epbReference The EPB reference to register
     *
     * @return void
     * @todo Replace lookupProxy callback with real proxy instance
     */
    protected function registerEpbReference(EpbReferenceDescriptorInterface $epbReference)
    {

        try {

            // load the application instance
            $application = $this->getApplication();

            // query whether the reference has already been bound to the application
            if ($application->search($name = $epbReference->getName())) { // if yes, do nothing

                // log a message that the reference has already been bound
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf('Reference php:global/%s/%s has already been bound to naming directory', $application->getName(), $name)
                );

                // return immediately
                return;
            }

        } catch (NamingException $e) { // catch the NamingException if the ref name is not bound yet

            // log a message that we've to register the EPB reference now
            $application->getInitialContext()->getSystemLogger()->debug(
                sprintf('Can\'t find php:global/%s/%s in naming directory', $application->getName(), $name)
            );
        }

        // this has to be refactored, because it'll be quite faster to inject either
        // the remote/local proxy instance as injection a callback that creates the
        // proxy on-the-fly!

        // prepare the bean name
        if ($beanName = str_replace('Bean', '', $epbReference->getBeanName())) {

            // query whether we've a local business interface
            if ($epbReference->getBeanInterface() === ($regName = sprintf('%sLocal', $beanName))) {

                // bind the local business interface of the bean to the appliations naming directory
                $application->bind($name, array(&$this, 'lookupProxy'), array($regName = sprintf('%s/local', $beanName)));

            // query whether we've a remote business interface
            } elseif ($epbReference->getBeanInterface() === ($regName = sprintf('%sRemote', $beanName))) {

                // bind the remote business interface of the bean to the applications naming directory
                $application->bind($name, array(&$this, 'lookupProxy'), array($regName = sprintf('%s/remote', $beanName)));

            // at least, we need a business interface
            } else {

                // log a critical message that we can't bind the reference
                $application->getInitialContext()->getSystemLogger()->critical(
                    sprintf('Can\'t bind php:global/%s/env/%s to naming directory', $name, $regName)
                );
            }

        // try to use the lookup, if we don't have the beanName
        } elseif ($lookup = $epbReference->getLookup()) {

            // create a reference to a bean in the global directory
            $application->getNamingDirectory()->bind($name, array(&$this, 'lookup'), array($lookup));

        } else { // log a critical message that we can't bind the reference

            $application->getInitialContext()->getSystemLogger()->critical(
                sprintf(
                    'Can\'t bind reference php:global/%s/%s to naming directory, because of missing source bean definition',
                    $application->getName(),
                    $name
                )
            );
        }
    }

    /**
     * Registers the passed resource reference in the applications directory.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\ResReferenceDescriptorInterface $resReference The resource reference to register
     *
     * @return void
     */
    protected function registerResReference(ResReferenceDescriptorInterface $resReference)
    {

        try {

            // load the application instance
            $application = $this->getApplication();

            // query whether the reference has already been bound to the application
            if ($application->search($name = $resReference->getName())) { // if yes, do nothing

                // log a message that the reference has already been bound
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf('Reference php:global/%s/%s has already been bound to naming directory', $application->getName(), $name)
                );

                // return immediately
                return;
            }

        } catch (NamingException $e) { // catch the NamingException if the ref name is not bound yet

            // log a message that we've to register the resource reference now
            $application->getInitialContext()->getSystemLogger()->debug($e->__toString());
        }

        // the reflection class for the passed type
        $reflectionClass = new ReflectionClass($resReference->getType());

        // bind a refererence to the resource shortname
        $application->bindReference($name, $reflectionClass->getShortName());
    }

    /**
     * This returns a proxy to the requested session bean.
     *
     * @param string $lookupName The lookup name for the requested session bean
     * @param string $sessionId  The session-ID if available
     *
     * @return \AppserverIo\Psr\PersistenceContainerProtocol\RemoteObject The proxy instance
     */
    public function lookupProxy($lookupName, $sessionId = null)
    {
        return $this->getInitialContext()->lookup($lookupName, $sessionId);
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClass($className)
    {
        return $this->getApplication()->search('ProviderInterface')->getReflectionClass($className);
    }

    /**
     * Returns a reflection class intance for the passed class name.
     *
     * @param object $instance The instance to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::newReflectionClass()
     * @see \DependencyInjectionContainer\Interfaces\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClassForObject($instance)
    {
        return $this->getApplication()->search('ProviderInterface')->getReflectionClassForObject($instance);
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string      $className The fully qualified class name to return the instance for
     * @param string|null $sessionId The session-ID, necessary to inject stateful session beans (SFBs)
     * @param array       $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     */
    public function newInstance($className, $sessionId = null, array $args = array())
    {
        return $this->getApplication()->search('ProviderInterface')->newInstance($className, $sessionId, $args);
    }
}
