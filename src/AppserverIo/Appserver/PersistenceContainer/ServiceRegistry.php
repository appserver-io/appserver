<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\ServiceRegistry
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

use AppserverIo\Storage\StorageInterface;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\EnterpriseBeans\ServiceContextInterface;
use AppserverIo\Psr\EnterpriseBeans\ServiceResourceLocatorInterface;

/**
 * The abstract service registry as base for implementations that handles applications services.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Storage\StorageInterface                            $data           The data storage to use
 * @property \AppserverIo\Psr\EnterpriseBeans\ServiceResourceLocatorInterface $serviceLocator The service locator
 * @property \AppserverIo\Storage\StorageInterface                            $services       The storage for the services
 * @property string                                                           $webappPath     The absolute path to this web application
 */
abstract class ServiceRegistry extends AbstractManager implements ServiceContextInterface, ManagerInterface
{

    /**
     * Injects the service locator to lookup the service.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\ServiceResourceLocatorInterface $serviceLocator The service locator
     *
     * @return void
     */
    public function injectServiceLocator(ServiceResourceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Injects the storage for the services.
     *
     * @param \AppserverIo\Storage\StorageInterface $services The storage for the services
     *
     * @return void
     */
    public function injectServices(StorageInterface $services)
    {
        $this->services = $services;
    }

    /**
     * Return the service locator instance.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\ServiceResourceLocatorInterface The service locator instance
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Return the storage with the services.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the services
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Tries to lookup and return the service with the passed name.
     *
     * @param string $serviceName The name of the requested service
     * @param array  $args        The arguments passed to the service providers constructor
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\ServiceProviderInterface The requested service instance
     */
    public function lookup($serviceName, array $args = array())
    {
        return $this->getServiceLocator()->lookup($this, $serviceName, $args);
    }
}
