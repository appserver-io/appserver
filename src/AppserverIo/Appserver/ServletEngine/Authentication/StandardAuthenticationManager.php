<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManager
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
 * @author     Florian Sydekum <fs@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Servlet\ServletContext;
use AppserverIo\Psr\Servlet\ServletRequest;
use AppserverIo\Psr\Servlet\ServletResponse;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The authentication manager handles request which need Http authentication.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Florian Sydekum <fs@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class StandardAuthenticationManager implements AuthenticationManager
{

    /**
     * Handles request in order to authenticate.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequest  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponse $servletResponse The response instance
     *
     * @return boolean TRUE if the authentication has been successfull, else FALSE
     */
    public function handleRequest(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {

        // load the actual context instance
        $context = $servletRequest->getContext();

        // iterate over all servlets and return the matching one
        foreach ($context->search('ServletContext')->getSecuredUrlConfigs() as $securedUrlConfig) {
            // continue if the can't find a config
            if ($securedUrlConfig == null) {
                continue;
            }

            // extract URL pattern and authentication configuration
            list ($urlPattern, $auth) = array_values($securedUrlConfig);

            // we'll match our URI against the URL pattern
            if (fnmatch($urlPattern, $servletRequest->getServletPath() . $servletRequest->getPathInfo())) {
                // load security configuration
                $configuredAuthType = $securedUrlConfig['auth']['auth-type'];

                // check the authentication type
                switch ($configuredAuthType) {
                    case "Basic":
                        $authImplementation =  'AppserverIo\Appserver\ServletEngine\Authentication\BasicAuthentication';
                        break;
                    case "Digest":
                        $authImplementation =  'AppserverIo\Appserver\ServletEngine\Authentication\DigestAuthentication';
                        break;
                    default:
                        throw new \Exception(sprintf('Unknown authentication type %s', $configuredAuthType));
                }

                // initialize the authentication manager
                $auth = new $authImplementation($securedUrlConfig);
                $auth->init($servletRequest, $servletResponse);

                // try to authenticate the request
                return $auth->authenticate();
            }
        }
    }

    /**
     * Initializes the manager instance.
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return AuthenticationManager::IDENTIFIER;
    }

    /**
     * Initializes the manager instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function initialize(ApplicationInterface $application)
    {
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     */
    public function getAttribute($key)
    {
        throw new \Exception(sprintf('%s is not implemented yes', __METHOD__));
    }
}
