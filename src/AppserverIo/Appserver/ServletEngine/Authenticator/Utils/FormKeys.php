<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authenticator\Utils\FormKeys
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
 * Utility class that contains the form keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FormKeys
{

    /**
     * The key for the username specified in the login form.
     *
     * @var string
     */
    const USERNAME = 'p_username';

    /**
     * The key for the password specified in the login in the form.
     *
     * @var string
     */
    const PASSWORD = 'p_password';

    /**
     * The key for a login form name.
     *
     * @var string
     */
    const FORM_ACTION = '/p_security_check';

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