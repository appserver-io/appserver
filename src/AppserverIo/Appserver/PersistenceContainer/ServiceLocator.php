<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\ServiceLocator
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException;
use AppserverIo\Psr\EnterpriseBeans\ServiceContextInterface;
use AppserverIo\Psr\EnterpriseBeans\ServiceResourceLocatorInterface;

/**
 * The service locator implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServiceLocator implements ServiceResourceLocatorInterface
{

    /**
     * Tries to lookup the service with the passed identifier.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\ServiceContextInterface $serviceContext    The service context instance
     * @param string                                                   $serviceIdentifier The identifier of the service to be looked up
     * @param array                                                    $args              The arguments passed to the service providers constructor
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\ServiceProviderInterface The requested service provider instance
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException Is thrown if the requested server can't be lookup up
     * @see \AppserverIo\Psr\EnterpriseBeans\ServiceResourceLocatorInterface::lookup()
     */
    public function lookup(ServiceContextInterface $serviceContext, $serviceIdentifier, array $args = array())
    {

        // first check if the service is available
        if ($serviceContext->getServices()->has($serviceIdentifier) === false) {
            throw new EnterpriseBeansException(
                sprintf(
                    'Requested service %s to handle %s is not available',
                    $serviceContext->getIdentifier(),
                    $serviceIdentifier
                )
            );
        }

        // return the initialized service instance
        return $serviceContext->getServices()->get($serviceIdentifier);
    }
}
