<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities\SharedStateKeys
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

namespace AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities;

/**
 * Utility class that contains the shared state keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SharedStateKeys
{

    /**
     * The key for the "servlet_engine.authentication.login_module.login_name" parameter.
     *
     * @var string
     */
    const LOGIN_NAME = 'servlet_engine.authentication.login_module.login_name';

    /**
     * The key for the "servlet_engine.authentication.login_module.login_password" parameter.
     *
     * @var string
     */
    const LOGIN_PASSWORD = 'servlet_engine.authentication.login_module.login_password';

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