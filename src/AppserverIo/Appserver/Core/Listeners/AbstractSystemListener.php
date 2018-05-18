<?php

/**
 * AppserverIo\Appserver\Core\Listeners\AbstractSystemListener
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

use League\Event\AbstractListener;
use AppserverIo\Psr\ApplicationServer\ApplicationServerInterface;

/**
 * Abstract application server aware system listener.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractSystemListener extends AbstractListener implements ApplicationServerAwareListenerInterface
{

    /**
     * The listeners application server instance.
     *
     * @var \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface
     */
    protected $applicationServer;

    /**
     * Injects the application server instance into the listener.
     *
     * @param \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface $applicationServer The application server instance to inject
     *
     * @return void
     */
    public function injectApplicationServer(ApplicationServerInterface $applicationServer)
    {
        $this->applicationServer = $applicationServer;
    }

    /**
     * Returns the application server instance of the listener.
     *
     * @return \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface The listeners application server instance
     */
    public function getApplicationServer()
    {
        return $this->applicationServer;
    }
}
