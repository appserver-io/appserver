<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationValve
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

use AppserverIo\Appserver\ServletEngine\ValveInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AuthenticationValve implements ValveInterface
{

    /**
     * Processes this valve (authenticate this request if necessary).
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function invoke(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // load the authentication manager
        /**
         * @var \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface $authenticationManager
         */
        $authenticationManager = $servletRequest->getContext()->search('AuthenticationManagerInterface');

        // authenticate the request
        if ($authenticationManager->handleRequest($servletRequest, $servletResponse) === false) {
            // dispatch this request, because we have to authenticate first
            $servletRequest->setDispatched(true);
        }
    }
}
