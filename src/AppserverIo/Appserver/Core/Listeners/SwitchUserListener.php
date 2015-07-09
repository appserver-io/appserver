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
use AppserverIo\Appserver\Core\Utilities\FileSystem;
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
     * @param \League\Event\EventInterface $event The triggering event
     *
     * @return void
     * @see \League\Event\ListenerInterface::handle()
     */
    public function handle(EventInterface $event)
    {

        try {
            // load the application server instance
            /** @var \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer */
            $applicationServer = $this->getApplicationServer();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // don't do anything under Windows
            if (FileSystem::getOsIdentifier() === 'WIN') {
                $applicationServer->getSystemLogger()->info('Don\'t switch UID to \'%s\' because OS is Windows');
                return;
            }

            // throw an exception if the POSIX extension is not available
            if (extension_loaded('posix') === false) {
                throw new \Exception('Can\'t switch user, because POSIX extension is not available');
            }

            // print a message with the old UID/EUID
            $applicationServer->getSystemLogger()->info("Running as " . posix_getuid() . "/" . posix_geteuid());

            // extract the variables
            $uid = 0;
            extract(posix_getpwnam($applicationServer->getSystemConfiguration()->getUser()));

            // switch the effective UID to the passed user
            if (posix_seteuid($uid) === false) {
                $applicationServer->getSystemLogger()->error(sprintf('Can\'t switch UID to \'%s\'', $uid));
            }

            // print a message with the new UID/EUID
            $applicationServer->getSystemLogger()->info("Running as user " . posix_getuid() . "/" . posix_geteuid());

            $gid = 0;
            extract(posix_getpwnam($applicationServer->getSystemConfiguration()->getGroup()));

            // switch the effective GID to the passed group
            if (posix_setegid($gid) === false) {
                $applicationServer->getSystemLogger()->error(sprintf('Can\'t switch GID to \'%s\'', $gid));
            }

            // print a message with the new GID/EGID
            $applicationServer->getSystemLogger()->info("Running as group" . posix_getgid() . "/" . posix_getegid());

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
