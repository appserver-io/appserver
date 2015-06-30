<?php

/**
 * AppserverIo\Appserver\Core\Listeners\ApplicationServerAwareListenerInterface
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

namespace AppserverIo\Appserver\Core\Listeners;

use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * Interface for application server aware listeners.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ApplicationServerAwareListenerInterface
{

    /**
     * Injects the application server instance into the listener.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer The application server instance to inject
     *
     * @return void
     */
    public function injectApplicationServer(ApplicationServerInterface $applicationServer);

    /**
     * Returns the application server instance of the listener.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface The listeners application server instance
     */
    public function getApplicationServer();
}
