<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Authentication\FormAuthentication
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

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Http\Authentication\AbstractAuthentication;
use AppserverIo\Http\Authentication\Adapters\HtpasswdAdapter;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FormAuthentication extends AbstractAuthentication
{

    /**
     * Defines the auth type which should match the client request type definition
     *
     * @var string AUTH_TYPE
     */
    const AUTH_TYPE = 'Form';

    /**
     * Defines the default authentication adapter used if none was specified
     *
     * @var string DEFAULT_ADAPTER
     */
    const DEFAULT_ADAPTER = HtpasswdAdapter::ADAPTER_TYPE;

    /**
     * Parses the given header content
     *
     * @param string $rawAuthData The raw authentication data coming from the client
     *
     * @return boolean If parsing was successful
     */
    protected function parse($rawAuthData)
    {
        // set auth hash got from auth data request header
        $authHash = trim(strstr($rawAuthData, " "));

        // check if username and password has been passed
        if (strstr($credentials = base64_decode($authHash), ':') === false) {
            return false;
        }

        // get out username and password
        list ($username, $password) = explode(':', $credentials);

        // check if either username or password was not found and return false
        if (($password === null) || ($username === null)) {
            return false;
        }

        // fill the auth data array
        $this->authData['username'] = $username;
        $this->authData['password'] = $password;
        return true;
    }

    /**
     * Returns the authentication header for response to set
     *
     * @return string
     */
    public function getAuthenticateHeader()
    {
        return $this->getType() . ' realm="' . $this->configData["realm"] . '"';
    }

    /**
     * Returns the parsed password
     *
     * @return string
     */
    public function getPassword()
    {
        $authData = $this->getAuthData();
        return isset($authData['password']) ? $authData['password'] : null;
    }
}
