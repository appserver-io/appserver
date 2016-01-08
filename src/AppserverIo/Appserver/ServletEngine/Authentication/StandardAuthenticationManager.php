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
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\ServletEngine\Authentication\DependencyInjection\DeploymentDescriptorParser;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Collections\HashMap;
use AppserverIo\Http\Authentication\AuthenticationException;

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
 * @property array                                 $securityDomains                          Contains the security domains
 * @property \AppserverIo\Storage\StorageInterface $authenticationMethods                    Contains all registered authentication methods
 * @property \AppserverIo\Storage\StorageInterface $urlPatternToAuthenticationMethodMappings Contains the URL pattern to authentication method mapping
 */
class StandardAuthenticationManager extends AbstractManager implements AuthenticationManagerInterface
{

    /**
     * Inject the storage for the authentication methods.
     *
     * @param \AppserverIo\Storage\StorageInterface $authenticationMethods The storage instance
     *
     * @return void
     */
    public function injectAuthenticationMethods(StorageInterface $authenticationMethods)
    {
        $this->authenticationMethods = $authenticationMethods;
    }

    /**
     * Inject the array with the security domains.
     *
     * @param array $securityDomains The security domains
     */
    public function injectSecurityDomains(array $securityDomains)
    {
        $this->securityDomains = $securityDomains;
    }

    /**
     * Inject the storage for the URL pattern to authentication method mappings.
     *
     * @param \AppserverIo\Storage\StorageInterface $urlPatternToAuthenticationMethodMappings The storage instance
     *
     * @return void
     */
    public function injectUrlPatternToAuthenticationMethodMappings(StorageInterface $urlPatternToAuthenticationMethodMappings)
    {
        $this->urlPatternToAuthenticationMethodMappings = $urlPatternToAuthenticationMethodMappings;
    }

    /**
     * Returns the array with the security domains.
     *
     * @return array The security domains
     */
    public function getSecurityDomains()
    {
        return $this->securityDomains;
    }

    /**
     * Register's the authentication method with the passed URL pattern.
     *
     * @param string $key                   The key to register the authentication method with
     * @param string $authenticationAdapter The authentication method
     *
     * @return void
     */
    public function addAuthenticationMethod($key, $authenticationMethodKey)
    {
        $this->authenticationMethods->set($key, $authenticationMethodKey);
    }

    /**
     * Returns the configured authentication method for the passed URL pattern authentication method mapping.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\UrlPatternToAuthenticationMethodMapping $urlPatternToAuthenticationMethodMapping The URL pattern to authentication method mapping
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the authentication methods
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the authentication method with the passed key is not available
     */
    public function getAuthenticationMethod(UrlPatternToAuthenticationMethodMapping $urlPatternToAuthenticationMethodMapping)
    {

        // query whether or not we've an authentication manager with the passed key
        if (isset($this->authenticationMethods[$authenticationMethodKey = $urlPatternToAuthenticationMethodMapping->getAuthenticationMethodKey()])) {
            return $this->authenticationMethods[$authenticationMethodKey];
        }

        // throw an exception if not
        throw new AuthenticationException(sprintf('Can\'t find authentication method for key %s', $authenticationMethodKey));
    }

    /**
     * Returns the configured authentication methods.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the authentication methods
     */
    public function getAuthenticationMethods()
    {
        return $this->authenticationMethods;
    }

    /**
     * Register's a new URL pattern to authentication method mapping.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\UrlPatternToAuthenticationMethodMapping $urlPatternToAuthenticationMethodMapping The URL pattern to authentication method mapping
     *
     * @return void
     */
    public function addUrlPatternToAuthenticationMethodMapping(UrlPatternToAuthenticationMethodMapping $urlPatternToAuthenticationMethodMapping)
    {
        $this->urlPatternToAuthenticationMethodMappings->set(
            $urlPatternToAuthenticationMethodMapping->getUrlPattern(),
            $urlPatternToAuthenticationMethodMapping
        );
    }

    /**
     * Return's the storage for the URL pattern to authentication method mappings.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage instance
     */
    public function getUrlPatternToAuthenticationMethodMappings()
    {
        return $this->urlPatternToAuthenticationMethodMappings;
    }

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
        /** @var \AppserverIo\Appserver\ServletEngine\Authentication\UrlPatternToAuthenticationMethodMapping $urlPatternToAuthenticationMethodMapping */
        foreach ($this->getUrlPatternToAuthenticationMethodMappings() as $urlPatternToAuthenticationMethodMapping) {
            try {
                // query whether or not the URI matches against the URL pattern
                if ($urlPatternToAuthenticationMethodMapping->match($servletRequest)) {
                    // load the authentication method
                    $authenticationMethod = $this->getAuthenticationMethod($urlPatternToAuthenticationMethodMapping);

                    // initialize and authenticate the request
                    $authenticationMethod->init($servletRequest, $servletResponse);
                    $authenticationMethod->authenticate($servletResponse);

                    // set authenticated username as a server var
                    $servletRequest->setRemoteUser($authenticationMethod->getUsername());

                    // stop processing, because we're already authenticated
                    break;
                }

            } catch (\Exception $e) {
                // load the system logger and debug log the exception
                if ($systemLogger = $this->getApplication()->getNamingDirectory()->search('php:global/log/System')) {
                    $systemLogger->debug($e->__toString());
                }

                // stop processing, because authentication failed for some reason
                return false;
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

        // query whether or not the web application folder exists
        if (is_dir($this->getWebappPath()) === false) {
            return;
        }

        $loginModules = new HashMap();

        /** @var \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNode $securityDomainNode */
        foreach ($this->getSecurityDomains() as $securityDomainNode) {

            /** @var \AppserverIo\Appserver\Core\Api\Node\AuthConfigNode $authConfigNode */
            foreach ($securityDomainNode->getAuthConfigs() as $authConfigNode) {

                /** @var \AppserverIo\Appserver\Core\Api\Node\LoginModuleNode $loginModuleNode */
                foreach ($authConfigNode->getLoginModules() as $loginModuleNode) {

                    $loginModuleType = $loginModuleNode->getType();
                    $securityDomainName = $securityDomainNode->getName()->__toString();

                    $loginModules->add($securityDomainName, new $loginModuleType($loginModuleNode->getParamsAsArray()));
                }
            }
        }

        // initialize the deployment descriptor parser and parse the web application's deployment descriptor for servlets
        $deploymentDescriptorParser = new DeploymentDescriptorParser();
        $deploymentDescriptorParser->injectAuthenticationContext($this);
        $deploymentDescriptorParser->parse();
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
