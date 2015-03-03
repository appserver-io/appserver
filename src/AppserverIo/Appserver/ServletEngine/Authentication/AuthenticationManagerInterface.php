<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface
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

use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;
use AppserverIo\Psr\Application\ManagerInterface;

/**
 * The interface for all authentication manager to implement.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface AuthenticationManagerInterface extends ManagerInterface
{

    /**
     * The unique identifier to be registered in the application context.
     *
     * @var string
     */
    const IDENTIFIER = 'AuthenticationManagerInterface';

    /**
     * Handles request in order to authenticate.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response instance
     *
     * @return boolean TRUE if the authentication has been successful, else FALSE
     */
    public function handleRequest(ServletRequestInterface $servletRequest, ServletResponseInterface $servletResponse);
}
