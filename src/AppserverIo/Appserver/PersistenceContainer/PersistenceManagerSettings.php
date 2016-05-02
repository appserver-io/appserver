<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\PersistenceManagerSettings
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

use AppserverIo\Appserver\Application\StandardManagerSettings;

/**
 * Default settings for the persistence container implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class PersistenceManagerSettings extends StandardManagerSettings implements PersistenceManagerSettingsInterface
{

    /**
     * The default base directory containing additional configuration information.
     *
     * @var string
     */
    const BASE_DIRECTORY = 'META-INF';

    /**
     * Initialize the default session settings.
     */
    public function __construct()
    {
        $this->setBaseDirectory(BeanManagerSettings::BASE_DIRECTORY);
    }
}
