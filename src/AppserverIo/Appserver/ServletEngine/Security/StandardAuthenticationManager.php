<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManager
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

namespace AppserverIo\Appserver\ServletEngine\Security;

use Psr\Log\LoggerInterface;
use AppserverIo\Collections\HashMap;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Appserver\ServletEngine\Security\DependencyInjection\DeploymentDescriptorParser;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Lang\String;

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
 * @property \AppserverIo\Collections\MapInterface $realms                       Contains the realms
 * @property \AppserverIo\Storage\StorageInterface $authenticationTypes          Contains all registered authentication types
 * @property \AppserverIo\Storage\StorageInterface $urlPatternToAuthTypeMappings Contains the URL pattern to authentication type mapping
 */
class StandardAuthenticationManager extends AbstractManager implements AuthenticationManagerInterface
{

    /**
     * Inject the storage for the authentication types.
     *
     * @param \AppserverIo\Storage\StorageInterface $authenticationTypes The storage instance
     *
     * @return void
     */
    public function injectAuthenticationTypes(StorageInterface $authenticationTypes)
    {
        $this->authenticationTypes = $authenticationTypes;
    }

    /**
     * Inject the map with the realms.
     *
     * @param \AppserverIo\Collections\MapInterface $realms The realms
     */
    public function injectRealms(MapInterface $realms)
    {
        $this->realms = $realms;
    }

    /**
     * Inject the storage for the URL pattern to authentication type mappings.
     *
     * @param \AppserverIo\Storage\StorageInterface $urlPatternToAuthTypeMappings The storage instance
     *
     * @return void
     */
    public function injectUrlPatternToAuthTypeMappings(StorageInterface $urlPatternToAuthTypeMappings)
    {
        $this->urlPatternToAuthTypeMappings = $urlPatternToAuthTypeMappings;
    }

    /**
     * Add's the passed realm the the authentication manager.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm The realm to add
     *
     * @return void
     */
    public function addRealm(RealmInterface $realm)
    {
        $this->realms->set($realm->getName(), $realm);
    }

    /**
     * Returns the map with the security domains.
     *
     * @return \AppserverIo\Collections\MapInterface The security domains
     */
    public function getRealms()
    {
        return $this->realms;
    }

    /**
     * Register's the authentication type with the passed URL pattern.
     *
     * @param string $key                   The key to register the authentication type with
     * @param string $authenticationAdapter The authentication type
     *
     * @return void
     */
    public function addAuthenticationType($key, $authTypeKey)
    {
        $this->authenticationTypes->set($key, $authTypeKey);
    }

    /**
     * Returns the configured authentication type for the passed URL pattern authentication type mapping.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Security\UrlPatternToAuthTypeMapping $urlPatternToAuthTypeMapping The URL pattern to authentication type mapping
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the authentication types
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the authentication type with the passed key is not available
     */
    public function getAuthenticationType(UrlPatternToAuthTypeMapping $urlPatternToAuthTypeMapping)
    {

        // query whether or not we've an authentication manager with the passed key
        if (isset($this->authenticationTypes[$authTypeKey = $urlPatternToAuthTypeMapping->getAuthTypeKey()])) {
            return $this->authenticationTypes[$authTypeKey];
        }

        // throw an exception if not
        throw new AuthenticationException(sprintf('Can\'t find authentication type for key %s', $authTypeKey));
    }

    /**
     * Returns the configured authentication types.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the authentication types
     */
    public function getAuthenticationTypes()
    {
        return $this->authenticationTypes;
    }

    /**
     * Register's a new URL pattern to authentication type mapping.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Security\UrlPatternToAuthTypeMapping $urlPatternToAuthTypeMapping The URL pattern to authentication type mapping
     *
     * @return void
     */
    public function addUrlPatternToAuthTypeMapping(UrlPatternToAuthTypeMapping $urlPatternToAuthTypeMapping)
    {
        $this->urlPatternToAuthTypeMappings->set(
            $urlPatternToAuthTypeMapping->getUrlPattern(),
            $urlPatternToAuthTypeMapping
        );
    }

    /**
     * Return's the storage for the URL pattern to authentication type mappings.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage instance
     */
    public function getUrlPatternToAuthTypeMappings()
    {
        return $this->urlPatternToAuthTypeMappings;
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

        // initialize authenticated flag
        $authenticated = true;

        // iterate over all servlets and return the matching one
        /** @var \AppserverIo\Appserver\ServletEngine\Security\UrlPatternToAuthTypeMapping $urlPatternToAuthTypeMapping */
        foreach ($this->getUrlPatternToAuthTypeMappings() as $urlPatternToAuthTypeMapping) {
            try {
                // query whether or not the URI matches against the URL pattern
                if ($urlPatternToAuthTypeMapping->match($servletRequest)) {
                    // query whether or not the the HTTP method has to be denied or authenticated
                    if (in_array($servletRequest->getMethod(), $urlPatternToAuthTypeMapping->getHttpMethodOmissions())) {
                        // this resource has to be omitted
                        $authenticated = false;
                    } elseif (in_array($servletRequest->getMethod(), $urlPatternToAuthTypeMapping->getHttpMethods())) {
                        // load the authentication method and authenticate the request
                        $authenticationMethod = $this->getAuthenticationType($urlPatternToAuthTypeMapping);
                        $authenticationMethod->authenticate($servletRequest, $servletResponse);

                        // initialize the roles flag
                        $inRole = false;

                        // query whether or not the user has at least one of the requested roles
                        foreach ($urlPatternToAuthTypeMapping->getRoleNames() as $role) {
                            if ($servletRequest->isUserInRole(new String($role))) {
                                $inRole = true;
                                break;
                            }
                        }

                        // if not, throw an SecurityException
                        if ($inRole === false) {
                            throw new SecurityException('User doesn\'t have necessary privileges');
                        }
                    }

                    // stop processing, because we processed authenticated
                    break;
                }

            } catch (\Exception $e) {
                // load the system logger and debug log the exception
                /** @var \Psr\Log\LoggerInterface $systemLogger */
                if ($systemLogger = $this->getApplication()->getNamingDirectory()->search(NamingDirectoryKeys::SYSTEM_LOGGER)) {
                    $systemLogger->error($e->__toString());
                }

                // stop processing, because authentication failed for some reason
                return false;
            }
        }

        // we did not find an adapter for that URI pattern, no authentication required then
        return $authenticated;
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

        // initialize the map for the realms
        $realms = new HashMap();

        // query whether or not we've manager configuration found
        /** @var \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerNode */
        if ($managerNode = $this->getManagerConfiguration()) {
            // initialize the security domains found in the manager configuration
            /** @var \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface $securityDomainNode */
            foreach ($this->getManagerConfiguration()->getSecurityDomains() as $securityDomainNode) {
                // create the realm instance
                $realm = new Realm($securityDomainNode->getName());
                $realm->injectConfiguration($securityDomainNode);
                // add the initialized security domain to the map
                $realms->add($realm->getName(), $realm);
            }
        }

        // inject the map with the realms
        $this->injectRealms($realms);

        // initialize the deployment descriptor parser and parse the web application's deployment descriptor for servlets
        $deploymentDescriptorParser = new DeploymentDescriptorParser();
        $deploymentDescriptorParser->injectAuthenticationContext($this);
        $deploymentDescriptorParser->parse();
    }

    /**
     * Returns the realm with the passed name.
     *
     * @param string $realmNam The name of the requested realm
     *
     * @return object The requested realm instance
     */
    public function getRealm($realm)
    {
        return $this->getRealms()->get($realm);
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
