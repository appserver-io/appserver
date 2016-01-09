<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\Callback\CallbackHandlerInterface
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
 * Interface for all callback handler implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface CallbackHandlerInterface
{

    /**
     * Handles UsernameCallback and PasswordCallback types. A UsernameCallback name property is set to the
     * Prinicpal->getName() value. A PasswordCallback password property is set to the credential value.
     *
     * @return void
     * @throws UnsupportedCallbackException Is thrown if any callback of type other than NameCallback or PasswordCallback has been passed
     */
    public function handle(CollectionInterface callbacks);
}
