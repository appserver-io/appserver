<?php

/**
 * AppserverIo\Appserver\ServletEngine\RequestContextInterface
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

namespace AppserverIo\Appserver\ServletEngine\Http;

use \AppserverIo\Psr\Context\ContextInterface;

/**
 * A Http servlet request interface.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface RequestContextInterface extends ContextInterface
{

    /**
     * Returns the session manager instance associated with this request.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionManagerInterface The session manager instance
     */
    public function getSessionManager();

    /**
     * Returns the authentication manager instance associated with this request.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface The authentication manager instance
     */
    public function getAuthenticationManager();
}
