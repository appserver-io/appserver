<?php

/**
 * AppserverIo\Appserver\Core\Listeners\SwitchUserListener
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

use League\Event\EventInterface;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * Listener that switches user/group to the values configured in system configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SwitchUserListener extends AbstractSystemListener
{

    /**
     * Handle an event.
     *
     * @param EventInterface $event
     *
     * @return void
     * @see \League\Event\ListenerInterface::handle()
     */
    public function handle(EventInterface $event)
    {

        try {
            // load the application server and the naming directory instance
            $applicationServer = $this->getApplicationServer();
            $namingDirectory = $applicationServer->getNamingDirectory();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // print a message with the old UID/EUID
            $applicationServer->getSystemLogger()->info("Running as " . posix_getuid() . "/" . posix_geteuid());

            // extract the variables
            $uid = 0;
            extract(posix_getpwnam($applicationServer->getSystemConfiguration()->getUser()));

            // switcht the effective UID to the passed user
            if (posix_seteuid($uid) === false) {
                $applicationServer->getSystemLogger()->error(sprintf('Can\'t switch UID to \'%s\'', $uid));
            }

            // print a message with the new UID/EUID
            $applicationServer->getSystemLogger()->info("Running as " . posix_getuid() . "/" . posix_geteuid());

            // @TODO Switch group also!!!! $applicationServer->getSystemConfiguration()->getGroup()

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
