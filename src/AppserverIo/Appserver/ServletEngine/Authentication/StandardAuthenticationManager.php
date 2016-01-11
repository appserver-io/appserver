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

use AppserverIo\Collections\HashMap;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Appserver\ServletEngine\Authentication\DependencyInjection\DeploymentDescriptorParser;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use Psr\Log\LoggerInterface;

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
 * @property \AppserverIo\Collections\MapInterface $securityDomains                          Contains the security domains
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
     * Inject the map with the security domains.
     *
     * @param \AppserverIo\Collections\MapInterface $securityDomains The security domains
     */
    public function injectSecurityDomains($securityDomains)
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
     * Add's the passed security domain the the authentication manager.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\SecurityDomainInterface $securityDomain The security domain to add
     *
     * @return void
     */
    public function addSecurityDomain(SecurityDomainInterface $securityDomain)
    {
        $this->securityDomains->set($securityDomain->getName(), $securityDomain);
    }

    /**
     * Returns the map with the security domains.
     *
     * @return \AppserverIo\Collections\MapInterface The security domains
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
                /** @var \Psr\Log\LoggerInterface $systemLogger */
                if ($systemLogger = $this->getApplication()->getNamingDirectory()->search(NamingDirectoryKeys::SYSTEM_LOGGER)) {
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

        // initialize the map for the security domains
        $securityDomains = new HashMap();

        // query whether or not we've manager configuration found
        /** @var \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerNode */
        if ($managerNode = $this->getManagerConfiguration()) {
            // initialize the security domains found in the manager configuration
            /** @var \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface $securityDomainNode */
            foreach ($this->getManagerConfiguration()->getSecurityDomains() as $securityDomainNode) {
                // create the security domain instance
                $securityDomain = new SecurityDomain($securityDomainNode->getName());
                $securityDomain->injectConfiguration($securityDomainNode);

                // add the initialized security domain to the map
                $securityDomains->add($securityDomain->getName(), $securityDomain);

                // register the security domain in the naming directory
                $this->getApplication()->getNamingDirectory()->bindCallback(sprintf('php:aas/%s/%s', $application->getName(), $securityDomain->getName()), array(&$this, 'lookup'));
            }
        }

        // inject the map with the security domains
        $this->injectSecurityDomains($securityDomains);

        // initialize the deployment descriptor parser and parse the web application's deployment descriptor for servlets
        $deploymentDescriptorParser = new DeploymentDescriptorParser();
        $deploymentDescriptorParser->injectAuthenticationContext($this);
        $deploymentDescriptorParser->parse();
    }

    /**
     * Returns the security domain with the passed name.
     *
     * @param string $lookupName The name of the session bean's class
     *
     * @return object The requested security domain instance
     */
    public function lookup($lookupName)
    {
        return $this->getSecurityDomains()->get($lookupName);
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
