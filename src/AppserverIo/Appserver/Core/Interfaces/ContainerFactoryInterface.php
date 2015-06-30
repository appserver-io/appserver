<?php

/**
 * \AppserverIo\Appserver\Core\Interfaces\ContainerFactoryInterface
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

namespace AppserverIo\Appserver\Core\Interfaces;

use AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * Interface for a factory to create new container instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ContainerFactoryInterface
{

    /**
     * Factory method to create a new container instance.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer The application instance to register the class loader with
     * @param \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface       $configuration     The class loader configuration
     *
     * @return void
     */
    public static function factory(ApplicationServerInterface $applicationServer, ContainerNodeInterface $configuration);
}
