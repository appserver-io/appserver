<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authenticator\Utils\SessionKeys
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

namespace AppserverIo\Appserver\ServletEngine\Authenticator\Utils;

/**
 * Utility class that contains the session keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SessionKeys
{

    /**
     * The key for the username stored in the session.
     *
     * @var string
     */
    const USERNAME = 'appserver_io.appserver.servlet_engine.security.utils.username';

    /**
     * The key for the password stored in the session.
     *
     * @var string
     */
    const PASSWORD = 'appserver_io.appserver.servlet_engine.security.utils.password';

    /**
     * The key for a complete form request stored in the session.
     *
     * @var string
     */
    const FORM_REQUEST = 'appserver_io.appserver.servlet_engine.security.utils.form_request';

    /**
     * This is a utility class, so protect it against direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }
}
