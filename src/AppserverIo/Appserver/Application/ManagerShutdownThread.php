<?php

/**
 * \AppserverIo\Appserver\Application\ManagerShutdownThread
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

namespace AppserverIo\Appserver\Application;

use AppserverIo\Psr\Application\ManagerInterface;

/**
 * Utility class that contains the application state keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ManagerShutdownThread extends \Thread
{

    /**
     * Initializes the thread with the manager to shutdown.
     *
     * @param \AppserverIo\Psr\Application\ManagerInterface $manager The manager to shutdown
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->start();
    }

    /**
     * Handles the clean manager shutdown.
     *
     * @return void
     */
    public function run()
    {

        // register the default autoloader
        require SERVER_AUTOLOADER;

        // create a local copy of the manager instance
        $manager = $this->manager;

        // query whether the manager has an application with class loaders to be registered
        if (method_exists($manager, 'getApplication') && $application = $manager->getApplication()) {
            $application->registerClassLoaders();
        }

        // stop the manager if an apropriate method exists
        if (method_exists($manager, 'stop')) {
            $manager->stop();
        }
    }
}
