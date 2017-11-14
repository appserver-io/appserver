<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\EnvironmentKeys
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

namespace AppserverIo\Appserver\Core\Utilities;

/**
 * Utility class that contains the application server's runlevels.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EnvironmentKeys
{

    /**
     * The environment key for the request ID of the actual execution environment.
     *
     * @var string
     */
    const REQUEST_ID = 'appserver_io.appserver.core.utilities.environment_keys.request_id';

    /**
     * The environment key for the session ID of the actual execution environment.
     *
     * @var string
     */
    const SESSION_ID = 'appserver_io.appserver.core.utilities.environment_keys.session_id';
}
