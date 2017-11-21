<?php

/**
 * \AppserverIo\Appserver\Core\AbstractEpbManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2016 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\RemoteMethodInvocation\LocalProxy;
use AppserverIo\Appserver\ServletEngine\RequestHandler;
use AppserverIo\Psr\EnterpriseBeans\BeanContextInterface;
use AppserverIo\Psr\EnterpriseBeans\PersistenceContextInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\EpbReferenceDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\ResReferenceDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\BeanReferenceDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\PersistenceUnitReferenceDescriptorInterface;

/**
 * Abstract manager which is able to handle EPB, resource and persistence unit registrations.
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2016 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */
abstract class AbstractEpbManager extends AbstractManager
{

    /**
     * Registers the passed EPB reference in the applications directory.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\EpbReferenceDescriptorInterface $epbReference The EPB reference to register
     *
     * @return void
     */
    public function registerEpbReference(EpbReferenceDescriptorInterface $epbReference)
    {

        try {
            // load the application instance and reference name
            $application = $this->getApplication();
            $name = $epbReference->getRefName();

            // initialize the bean's URI
            $uri = sprintf('php:global/%s/%s', $application->getUniqueName(), $name);

            // this has to be refactored, because it'll be quite faster to inject either
            // the remote/local proxy instance as injection a callback that creates the
            // proxy on-the-fly!

            // prepare the bean name
            if ($beanName = $epbReference->getBeanName()) {
                // query whether we've a local business interface
                if ($epbReference->getBeanInterface() === sprintf('%sLocal', $beanName)) {
                    // bind the local business interface of the bean to the appliations naming directory
                    $application->getNamingDirectory()
                                ->bind(
                                    $uri,
                                    array(&$this, 'lookupProxy'),
                                    array(sprintf('%s/local', $beanName))
                                );

                // query whether we've a remote business interface
                } elseif ($epbReference->getBeanInterface() === (sprintf('%sRemote', $beanName))) {
                    // bind the remote business interface of the bean to the applications naming directory
                    $application->getNamingDirectory()
                                ->bind(
                                    $uri,
                                    array(&$this, 'lookupProxy'),
                                    array(sprintf('%s/remote', $beanName))
                                );

                // at least, we need a business interface
                } else {
                    // log a critical message that we can't bind the reference
                    $application->getInitialContext()->getSystemLogger()->critical(
                        sprintf('Can\'t bind bean reference %s to naming directory', $uri)
                    );
                }

            // try to use the lookup, if we don't have the beanName
            } elseif ($lookup = $epbReference->getLookup()) {
                // create a reference to a bean in the global directory
                $application->getNamingDirectory()->bind($uri, array(&$this, 'lookup'), array($lookup));

            // log a critical message that we can't bind the reference
            } else {
                $application->getInitialContext()->getSystemLogger()->critical(
                    sprintf('Can\'t bind enterprise bean reference %s to naming directory, because of missing source bean definition', $uri)
                );
            }

        // catch the the exception that occures if a reference has already been created
        } catch (NamingException $e) {
            // log a warning that the reference has already been registered
            $application->getInitialContext()->getSystemLogger()->warning(
                sprintf('Bean reference %s already exists', $uri)
            );

        // catch all other exceptions
        } catch (\Exception $e) {
            $application->getInitialContext()->getSystemLogger()->critical($e->__toString());
        }
    }

    /**
     * Registers the passed resource reference in the applications directory.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\ResReferenceDescriptorInterface $resReference The resource reference to register
     *
     * @return void
     */
    public function registerResReference(ResReferenceDescriptorInterface $resReference)
    {
        try {
            // load the application instance and reference name
            $application = $this->getApplication();

            // initialize the resource URI
            $uri = sprintf('php:global/%s/%s', $application->getUniqueName(), $resReference->getRefName());

            // query whether the reference has already been bound to the application
            if ($application->getNamingDirectory()->search($uri)) {
                // log a message that the reference has already been bound
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf('Resource reference %s has already been bound to naming directory', $uri)
                );

                // return immediately
                return;
            }

        // catch the NamingException if the ref name is not bound yet
        } catch (NamingException $e) {
            // log a message that we've to register the resource reference now
            $application->getInitialContext()->getSystemLogger()->debug(
                sprintf('Resource reference %s has not been bound to naming directory', $uri)
            );
        }

        try {
            // try to use the lookup to bind the reference to
            if ($lookup = $resReference->getLookup()) {
                // create a reference to a resource in the global directory
                $application->getNamingDirectory()->bindReference($uri, $lookup);

            // try to bind the reference by the specified type
            } elseif ($type = $resReference->getType()) {
                // bind a reference to the resource shortname
                $application->getNamingDirectory()
                            ->bindReference(
                                $uri,
                                sprintf('php:global/%s/%s', $application->getUniqueName(), $type)
                            );

            // log a critical message that we can't bind the reference
            } else {
                $application->getInitialContext()->getSystemLogger()->critical(
                    sprintf('Can\'t bind resource reference %s to naming directory, because of missing source bean definition', $uri)
                );
            }

        // catch all other exceptions
        } catch (\Exception $e) {
            $application->getInitialContext()->getSystemLogger()->critical($e->__toString());
        }
    }

    /**
     * Registers the passed bean reference in the applications directory.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\BeanReferenceDescriptorInterface $beanReference The bean reference to register
     *
     * @return void
     */
    public function registerBeanReference(BeanReferenceDescriptorInterface $beanReference)
    {
        try {
            // load the application instance and reference name
            $application = $this->getApplication();

            // initialize the class URI
            $uri = sprintf('php:global/%s/%s', $application->getUniqueName(), $beanReference->getRefName());

            // query whether the reference has already been bound to the application
            if ($application->getNamingDirectory()->search($uri)) {
                // log a message that the reference has already been bound
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf('Bean reference %s has already been bound to naming directory', $uri)
                );

                // return immediately
                return;
            }

        // catch the NamingException if the ref name is not bound yet
        } catch (NamingException $e) {
            // log a message that we've to register the bean reference now
            $application->getInitialContext()->getSystemLogger()->debug(
                sprintf('Bean reference %s has not been bound to naming directory', $uri)
            );
        }

        try {
            // try to bind the bean by the specified name
            if ($name = $beanReference->getName()) {
                // bind a reference to the class type
                $application->getNamingDirectory()
                            ->bind(
                                $uri,
                                array(&$this, 'lookupBean'),
                                array($name)
                            );

            // log a critical message that we can't bind the reference
            } else {
                $application->getInitialContext()->getSystemLogger()->critical(
                    sprintf('Can\'t bind bean reference %s to naming directory, because of missing source bean definition', $uri)
                );
            }

        // catch all other exceptions
        } catch (\Exception $e) {
            $application->getInitialContext()->getSystemLogger()->critical($e->__toString());
        }
    }

    /**
     * Registers the passed persistence unit reference in the applications directory.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\PersistenceUnitReferenceDescriptorInterface $persistenceUnitReference The persistence unit reference to register
     *
     * @return void
     */
    public function registerPersistenceUnitReference(PersistenceUnitReferenceDescriptorInterface $persistenceUnitReference)
    {
        try {
            // load the application instance and reference name
            $application = $this->getApplication();

            // initialize the persistence unit URI
            $uri = sprintf('php:global/%s/%s', $application->getUniqueName(), $persistenceUnitReference->getRefName());

            // query whether the reference has already been bound to the application
            if ($application->getNamingDirectory()->search($uri)) {
                // log a message that the reference has already been bound
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf('Persistence unit reference %s has already been bound to naming directory', $uri)
                );

                // return immediately
                return;
            }

        // catch the NamingException if the ref name is not bound yet
        } catch (NamingException $e) {
            // log a message that we've to register the resource reference now
            $application->getInitialContext()->getSystemLogger()->info(
                sprintf('Persistence unit reference %s has not been bound to naming directory', $uri)
            );
        }

        try {
            // try to use the unit name to bind the reference to
            if ($unitName = $persistenceUnitReference->getUnitName()) {
                // load the persistenc manager to bind the callback to
                $persistenceManager = $application->search(PersistenceContextInterface::IDENTIFIER);
                // create a reference to a persistence unit in the global directory
                $application->getNamingDirectory()
                            ->bind(
                                $uri,
                                array(&$persistenceManager, 'lookupProxy'),
                                array(sprintf('php:global/%s/%s', $application->getUniqueName(), $unitName))
                            );

            // log a critical message that we can't bind the reference
            } else {
                $application->getInitialContext()->getSystemLogger()->critical(
                    sprintf('Can\'t bind persistence unit Reference %s to naming directory, because of missing unit name definition', $uri)
                );
            }

        // catch all other exceptions
        } catch (\Exception $e) {
            $application->getInitialContext()->getSystemLogger()->critical($e->__toString());
        }
    }

    /**
     * This returns an instance of the requested bean.
     *
     * @param string $lookupName The lookup name for the requested bean
     * @param string $sessionId  The session-ID if available
     *
     * @return object The bean instance
     */
    public function lookupBean($lookupName, $sessionId = null)
    {
        return $this->getApplication()->search($lookupName, array($sessionId, array($this->getApplication())));
    }

    /**
     * This returns a proxy to the requested session bean. If the proxy has already been
     * instanciated for the actual request, the existing instance will be returned.
     *
     * @param string $lookupName The lookup name for the requested session bean
     * @param string $sessionId  The session-ID if available
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The proxy instance
     */
    public function lookupProxy($lookupName, $sessionId = null)
    {

        // query whether or not a proxy has already been loaded for the actual execution environment (request)
        if (!Environment::singleton()->hasAttribute($lookupName)) {
            // load the initial context instance
            $initialContext = $this->getInitialContext();

            // query whether a request context is available
            if ($servletRequest = RequestHandler::getRequestContext()) {
                // inject the servlet request to handle SFSBs correctly
                $initialContext->injectServletRequest($servletRequest);
            }

            // lookup the proxy by the name and session ID if available
            Environment::singleton()->setAttribute($lookupName, $initialContext->lookup($lookupName, $sessionId));
        }

        // return the proxy from the actual execution environment (request)
        return Environment::singleton()->getAttribute($lookupName);
    }

    /**
     * This returns a local proxy to the requested session bean.
     *
     * @param string $lookupName The lookup name for the requested session bean
     * @param string $sessionId  The session-ID if available
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The proxy instance
     */
    public function lookupLocalProxy($lookupName, $sessionId = null)
    {

        // extract the session bean name from the lookup name
        $beanName = str_replace('/local', '', $lookupName);

        // load the application
        $application = $this->getApplication();

        // load bean and object manager
        $beanManager = $application->search(BeanContextInterface::IDENTIFIER);
        $objectManager = $application->search(ObjectManagerInterface::IDENTIFIER);

        // load the requested session bean
        $sessionBean = $application->search($beanName, array($sessionId));

        // load the bean descriptor
        $sessionBeanDescriptor = $objectManager->getObjectDescriptors()->get(get_class($sessionBean));

        // initialize the local proxy instance
        return new LocalProxy(
            $beanManager,
            $sessionBeanDescriptor,
            $sessionBean,
            $sessionId
        );
    }
}
