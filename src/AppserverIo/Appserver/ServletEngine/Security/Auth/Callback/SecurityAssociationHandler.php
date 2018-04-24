<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Callback\SecurityAssociationHandler
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

namespace AppserverIo\Appserver\ServletEngine\Security\Auth\Callback;

use AppserverIo\Lang\String;
use AppserverIo\Collections\CollectionInterface;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Security\Auth\Callback\NameCallback;
use AppserverIo\Psr\Security\Auth\Callback\PasswordCallback;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Psr\Security\Auth\Callback\UnsupportedCallbackException;

/**
 * An abstract login module implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SecurityAssociationHandler implements CallbackHandlerInterface
{

    /**
     * The principal instance.
     *
     * @var \AppserverIo\Psr\Security\PrincipalInterface
     */
    protected $principal;

    /**
     * The principal's credential.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $credential;

    /**
     *
     * @param \AppserverIo\Psr\Security\PrincipalInterface $principal  The principal instance
     * @param \AppserverIo\Lang\String                     $credential The principal's credential
     */
    public function __construct(PrincipalInterface $principal, String $credential)
    {
        $this->principal = $principal;
        $this->credential = $credential;
    }

    /**
     * Handles UsernameCallback and PasswordCallback types. A UsernameCallback name property is set to the
     * Prinicpal->getName() value. A PasswordCallback password property is set to the credential value.
     *
     * @param \AppserverIo\Collections\CollectionInterface $callbacks The collection with the callbacks
     *
     * @return void
     * @throws \AppserverIo\Psr\Security\Auth\Callback\UnsupportedCallbackException Is thrown if any callback of type other than NameCallback or PasswordCallback has been passed
     */
    public function handle(CollectionInterface $callbacks)
    {
        // handle the registered callbacks
        foreach ($callbacks as $callback) {
            if ($callback instanceof NameCallback) {
                $callback->setName($this->principal->getName());
            } elseif ($callback instanceof PasswordCallback) {
                $callback->setPassword($this->credential);
            } else {
                throw new UnsupportedCallbackException('Unrecognized Callback');
            }
        }
    }
}
