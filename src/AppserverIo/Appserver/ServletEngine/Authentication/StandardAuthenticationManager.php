<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Florian Sydekum <fs@techdivision.com>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Http\HttpProtocol;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The authentication manager handles request which need Http authentication.
 *
 * @author    Florian Sydekum <fs@techdivision.com>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\WebServer\Interfaces\AuthenticationInterface[] $authenticationAdapters Contains all registered authentication adapters sorted by URI pattern
 */
class StandardAuthenticationManager extends AbstractManager implements AuthenticationManagerInterface
{

    /**
     * Handles request in order to authenticate.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return boolean TRUE if the authentication has been successful, else FALSE
     *
     * @throws \Exception
     */
    public function handleRequest(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // iterate over all servlets and return the matching one
        /**
         * @var string $urlPattern
         * @var \AppserverIo\Http\Authentication\AuthenticationInterface $authenticationAdapter
         */
        foreach ($this->authenticationAdapters as $urlPattern => $authenticationAdapter) {
            // we'll match our URI against the URL pattern

            if (fnmatch($urlPattern, $servletRequest->getServletPath() . $servletRequest->getPathInfo())) {
                // the URI pattern matches, init the adapter and try to authenticate

                $authenticationAdapter->init($servletRequest->getHeader(HttpProtocol::HEADER_AUTHORIZATION), $servletRequest->getMethod());

                // try to authenticate the request
                $authenticated = $authenticationAdapter->authenticate();
                if (!$authenticated) {
                    // send header for challenge authentication against client
                    $servletResponse->setStatusCode(401);
                    $servletResponse->addHeader(HttpProtocol::HEADER_WWW_AUTHENTICATE, $authenticationAdapter->getAuthenticateHeader());
                }

                return $authenticated;
            }
        }

        // we did not find an adapter for that URI pattern, no authentication required then
        return true;
    }

    /**
     * Initializes the manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return AuthenticationManagerInterface::IDENTIFIER;
    }

    /**
     * Initializes the manager instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     *
     * @throws \Exception
     */
    public function initialize(ApplicationInterface $application)
    {

        // iterate over all servlets and return the matching one
        $authenticationAdapters = array();
        foreach ($application->search('ServletContextInterface')->getSecuredUrlConfigs() as $securedUrlConfig) {
            // continue if the can't find a config
            if ($securedUrlConfig == null) {
                continue;
            }

            // extract URL pattern and authentication configuration
            list ($urlPattern, $auth) = array_values($securedUrlConfig);
            // load security configuration
            $configuredAuthType = $securedUrlConfig['auth']['auth-type'];

            // check the authentication type
            switch ($configuredAuthType) {
                case "Basic":
                    $authImplementation =  '\AppserverIo\Http\Authentication\BasicAuthentication';
                    break;
                case "Digest":
                    $authImplementation =  '\AppserverIo\Http\Authentication\DigestAuthentication';
                    break;
                default:
                    throw new \Exception(sprintf('Unknown authentication type %s', $configuredAuthType));
            }

            // in preparation we have to flatten the configuration structure
            $config = $securedUrlConfig['auth'];
            array_shift($config);
            $options = $config['options'];
            unset($config['options']);

            // we do need to make some alterations
            if (isset($options['file'])) {
                $options['file'] = $application->getWebappPath() . DIRECTORY_SEPARATOR . $options['file'];
            }

            // initialize the authentication manager
            /** @var \AppserverIo\Http\Authentication\AuthenticationInterface $auth */
            $auth = new $authImplementation(
                array_merge(
                    array('type' => $authImplementation),
                    $config,
                    $options
                )
            );
            $authenticationAdapters[$urlPattern] = $auth;
        }

        $this->authenticationAdapters = $authenticationAdapters;
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
        throw new \Exception(sprintf('%s is not implemented yet', __METHOD__));
    }
}
