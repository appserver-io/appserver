<?php

/**
 * \AppserverIo\Appserver\Core\GenericContainerFactory
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
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Interfaces\ContainerFactoryInterface;
use AppserverIo\Psr\ApplicationServer\ApplicationServerInterface;
use AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface;

/**
 * A factory for the container instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class GenericContainerFactory implements ContainerFactoryInterface
{

    /**
     * Factory method to create a new container instance.
     *
     * @param \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface                    $applicationServer The application instance to register the class loader with
     * @param \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $configuration     The class loader configuration
     * @param integer                                                                          $runlevel          The runlevel the container has been started in
     *
     * @return void
     */
    public static function factory(
        ApplicationServerInterface $applicationServer,
        ContainerConfigurationInterface $configuration,
        $runlevel = ApplicationServerInterface::NETWORK
    ) {

        // create a new reflection class instance
        $reflectionClass = new \ReflectionClass($configuration->getType());

        // initialize the container configuration with the base directory and pass it to the thread
        $params = array($applicationServer->getInitialContext(), $applicationServer->getNamingDirectory(), $configuration, $applicationServer->runlevelToString($runlevel));

        // create, initialize and return the container instance
        return $reflectionClass->newInstanceArgs($params);
    }
}
