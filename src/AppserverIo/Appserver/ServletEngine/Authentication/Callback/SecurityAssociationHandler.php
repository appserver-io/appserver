<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\Callback\SecurityAssociationHandler
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

namespace AppserverIo\Appserver\ServletEngine\Authentication\Callback;

use AppserverIo\Collections\CollectionInterface;

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
     *
     * @var unknown
     */
    protected $principal;

    /**
     *
     * @var unknown
     */
    protected $credential;

    /**
     *
     * @param unknown $principal
     * @param unknown $credential
     */
    public function __construct(Principal $principal, $credential)
    {
        $this->principal = $principal;
        $this->credential = $credential;
    }

    /**
     * Handles UsernameCallback and PasswordCallback types. A UsernameCallback name property is set to the
     * Prinicpal->getName() value. A PasswordCallback password property is set to the credential value.
     *
     * @return void
     * @throws UnsupportedCallbackException Is thrown if any callback of type other than NameCallback or PasswordCallback has been passed
     */
    public function handle(CollectionInterface callbacks)
    {

        foreach ($callbacks as $callback) {

            if ($callback instanceof UsernameCallback) {
                $callback->setUsername($this->principal->getName());
            } elseif ($callback instanceof PasswordCallback) {
                $callback->setPassword($this->credential);
            } else {
                throw new UnsupportedCallbackException("Unrecognized Callback");
            }
        }
    }
}
