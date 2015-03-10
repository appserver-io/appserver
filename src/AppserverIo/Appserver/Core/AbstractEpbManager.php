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
 * @category   Server
 * @package    Appserver
 * @subpackage Core
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\EnterpriseBeans\Description\EpbReferenceDescriptorInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\ResReferenceDescriptorInterface;

/**
 * Abstract manager which is able to handle EPB and resource registrations
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Core
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
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
     * @todo Replace lookupProxy callback with real proxy instance
     */
    public function registerEpbReference(EpbReferenceDescriptorInterface $epbReference)
    {

        try {
            // load the application instance and reference name
            $application = $this->getApplication();
            $name = $epbReference->getName();

            // this has to be refactored, because it'll be quite faster to inject either
            // the remote/local proxy instance as injection a callback that creates the
            // proxy on-the-fly!

            // prepare the bean name
            if ($beanName = $epbReference->getBeanName()) {
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

            // log a critical message that we can't bind the reference
            } else {
                $application->getInitialContext()->getSystemLogger()->critical(
                    sprintf(
                        'Can\'t bind reference php:global/%s/%s to naming directory, because of missing source bean definition',
                        $application->getName(),
                        $name
                    )
                );
            }

        // catch the the exception that occures if a reference has already been created
        } catch (NamingException $e) {
            // log a warning that the reference has already been registered
            $application->getInitialContext()->getSystemLogger()->warning(
                sprintf('Reference php:global/%s/%s already exists', $application->getName(), $name)
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

            // query whether the reference has already been bound to the application
            if ($application->search($name = $resReference->getName())) {
                // log a message that the reference has already been bound
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf('Reference php:global/%s/%s has already been bound to naming directory', $application->getName(), $name)
                );

                // return immediately
                return;
            }

            // catch the NamingException if the ref name is not bound yet
        } catch (NamingException $e) {
            // log a message that we've to register the resource reference now
            $application->getInitialContext()->getSystemLogger()->debug(
                sprintf('Reference php:global/%s/%s has not been bound to naming directory', $application->getName(), $name)
            );
        }

        try {
            // try to use the lookup to bind the reference to
            if ($lookup = $resReference->getLookup()) {
                // create a reference to a resource in the global directory
                $application->bindReference($name, $lookup);

            // try to bind the reference by the specified type
            } elseif ($type = $resReference->getType()) {
                // the reflection class for the passed type
                $reflectionClass = new ReflectionClass($resReference->getType());

                // bind a reference to the resource shortname
                $application->bindReference($name, $reflectionClass->getShortName());

            // log a critical message that we can't bind the reference
            } else {
                $application->getInitialContext()->getSystemLogger()->critical(
                    sprintf(
                        'Can\'t bind reference php:global/%s/%s to naming directory, because of missing source bean definition',
                        $application->getName(),
                        $name
                    )
                );
            }

        // catch all other exceptions
        } catch (\Exception $e) {
            $application->getInitialContext()->getSystemLogger()->critical($e->__toString());
        }
    }
}
