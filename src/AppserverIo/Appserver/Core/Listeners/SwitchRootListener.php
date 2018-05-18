<?php

/**
 * AppserverIo\Appserver\Core\Listeners\SwitchRootListener
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

/**
 * Listener that switches back to root as extended user/group.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SwitchRootListener extends AbstractSystemListener
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
            /** @var \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface $applicationServer */
            $applicationServer = $this->getApplicationServer();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // print a message with the old UID/EUID
            $applicationServer->getSystemLogger()->info("Running as " . posix_getuid() . "/" . posix_geteuid());

            // extract the variables
            $uid = 0;
            extract(posix_getpwnam('root'));

            // switcht the effective UID to the passed user
            if (posix_seteuid($uid) === false) {
                $applicationServer->getSystemLogger()->error(sprintf('Can\'t switch UID to \'%s\'', $uid));
            }

            // print a message with the new UID/EUID
            $applicationServer->getSystemLogger()->info("Running as " . posix_getuid() . "/" . posix_geteuid());

            // @TODO Switch group also!!!!

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
