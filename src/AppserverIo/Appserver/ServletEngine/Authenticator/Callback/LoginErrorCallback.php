<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authenticator\Callback\LoginErrorCallback
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

namespace AppserverIo\Appserver\ServletEngine\Authenticator\Callback;

use AppserverIo\Collections\ArrayList;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\ServletEngine\Security\RealmInterface;
use AppserverIo\Appserver\ServletEngine\Authenticator\Utils\SessionKeys;

/**
 * A callback implementation that implements the default login error
 * handling for the form based authentication type.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LoginErrorCallback implements CallbackInterface
{

    /**
     * Will be invoked by authenticator to allow custom login error handling.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm           The realm instance containing the exception stack
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface    $servletRequest  The session instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface   $servletResponse The session instance
     *
     * @return void
     */
    public function handle(
        RealmInterface $realm,
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // load the session from the request
        if ($session = $servletRequest->getSession()) {
            // prepare the ArrayList for the login errors
            $formErrors = new ArrayList();

            // transform the realm's exception stack into simple error messages
            foreach ($realm->getExceptionStack() as $e) {
                $formErrors->add($e->getMessage());
            }

            // add the error messages to the session
            $session->putData(SessionKeys::FORM_ERRORS, $formErrors);
        }
    }
}
