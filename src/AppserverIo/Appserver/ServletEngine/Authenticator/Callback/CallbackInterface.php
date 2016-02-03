<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authenticator\Callback\CallbackInterface
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

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\ServletEngine\Security\RealmInterface;

/**
 * Interface for form based authentication method login error callbacks.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface CallbackInterface
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
    );
}
