<?php

/**
 * AppserverIo\Appserver\ServletEngine\RequestContext
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Http;

use \AppserverIo\Psr\Context\Context;

/**
 * A Http servlet request interface.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface RequestContext extends Context
{
    
    /**
     * Returns the session manager instance associated with this request.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionManager The session manager instance
     */
    public function getSessionManager();
    
    /**
     * Returns the authentication manager instance associated with this request.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManager The authentication manager instance
     */
    public function getAuthenticationManager();
}
