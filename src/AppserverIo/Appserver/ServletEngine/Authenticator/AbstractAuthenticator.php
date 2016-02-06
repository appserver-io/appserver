<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authenticator\AbstractAuthenticator
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

namespace AppserverIo\Appserver\ServletEngine\Authenticator;

use Rhumsaa\Uuid\Uuid;
use AppserverIo\Lang\Boolean;
use AppserverIo\Appserver\Core\Api\Node\LoginConfigNodeInterface;
use AppserverIo\Appserver\ServletEngine\Security\AuthenticationManagerInterface;

/**
 * Abstract authenticator base class providing generic functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractAuthenticator implements AuthenticatorInterface
{

    /**
     * The UUID of the authenticator.
     *
     * @var string
     */
    protected $serial;

    /**
     * Mark's the authenticator as the default one.
     *
     * @var \AppserverIo\Lang\Boolean
     */
    protected $defaultAuthenticator;

    /**
     * Holds the configuration data given for authentication type.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\LoginConfigNodeInterface
     */
    protected $configData;

    /**
     * The authentication manager instance.
     *
     * @var \AppserverIo\Appserver\ServletEngine\Security\AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * The name of the user to authenticate.
     *
     * @var string
     */
    protected $username;

    /**
     * Constructs the authentication type.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\LoginConfigNodeInterface                $configData            The configuration data for auth type instance
     * @param \AppserverIo\Appserver\ServletEngine\Security\AuthenticationManagerInterface $authenticationManager The authentication manager instance
     * @param \AppserverIo\Lang\Boolean                                                    $defaultAuthenticator  The flag for the default authenticator
     */
    public function __construct(
        LoginConfigNodeInterface $configData,
        AuthenticationManagerInterface $authenticationManager,
        Boolean $defaultAuthenticator = null
    ) {

        // create a UUID serial for the authenticator
        $this->serial = Uuid::uuid4()->__toString();

        // initialize the authenticator with the passed values
        $this->configData = $configData;
        $this->authenticationManager = $authenticationManager;

        // query whether or not the default flag has been passed
        if ($defaultAuthenticator == null) {
            $this->defaultAuthenticator = new Boolean(false);
        } else {
            $this->defaultAuthenticator = $defaultAuthenticator;
        }
    }

    /**
     * Return's the authenticator's UUID.
     *
     * @return string The UUID
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Returns the configuration data given for authentication type.
     *
     * @return object The configuration data
     */
    public function getConfigData()
    {
        return $this->configData;
    }

    /**
     * Return's the authentication manager instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Security\AuthenticationManagerInterface The authentication manager instance
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * Returns the authentication type token.
     *
     * @return string
     */
    public function getAuthType()
    {
        return static::AUTH_TYPE;
    }

    /**
     * Return's the realm name.
     *
     * @return string The realm name
     */
    public function getRealmName()
    {
        return $this->getConfigData()->getRealmName();
    }

    /**
     * Returns the parsed username.
     *
     * @return string|null The username
     */
    public function getUsername()
    {
        return isset($this->username) ? $this->username : null;
    }

    /**
     * Mark's the authenticator as the default one.
     *
     * @return void
     */
    public function setDefaultAuthenticator()
    {
        $this->defaultAuthenticator = new Boolean(true);
    }

    /**
     * Query whether or not this is the default authenticator.
     *
     * @return boolean TRUE if this is the default authenticator, else FALSE
     */
    public function isDefaultAuthenticator()
    {
        return $this->defaultAuthenticator->booleanValue();
    }
}
