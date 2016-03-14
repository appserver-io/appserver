<?php

/**
 * AppserverIo\Appserver\Application\Interfaces\ManagerSettingsAwareInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application\Interfaces;

use AppserverIo\Appserver\Naming\InitialContextAwareInterface;

/**
 * Interface for manager settings aware manager implemenations.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ManagerSettingsAwareInterface extends InitialContextAwareInterface
{

    /**
     * Return's the manager settings.
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface The manager settings
     */
    public function getManagerSettings();

    /**
     * Inject's the manager settings.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface $managerSettings The manager settings
     *
     * @return void
     */
    public function injectManagerSettings(ManagerSettingsInterface $managerSettings);
}
