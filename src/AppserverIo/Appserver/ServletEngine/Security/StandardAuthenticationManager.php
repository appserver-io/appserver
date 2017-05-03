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
use AppserverIo\Lang\String;
use AppserverIo\Collections\HashMap;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Psr\Security\Utils\Constants;
use AppserverIo\Psr\Auth\AuthenticatorInterface;
use AppserverIo\Psr\Auth\AuthenticationManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\Utils\RequestHandlerKeys;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Appserver\ServletEngine\Security\DependencyInjection\DeploymentDescriptorParser;
use AppserverIo\Psr\Auth\RealmInterface;
use AppserverIo\Psr\Auth\MappingInterface;
use AppserverIo\Psr\Security\SecurityException;

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
 * @property \AppserverIo\Collections\MapInterface $realms         Contains the realms
 * @property \AppserverIo\Storage\StorageInterface $authenticators Contains all registered authenticators
 * @property \AppserverIo\Storage\StorageInterface $mappings       Contains the URL pattern to authenticator mapping
 */
class StandardAuthenticationManager extends AbstractManager implements AuthenticationManagerInterface
{

    /**
     * Inject the storage for the authenticators.
     *
     * @param \AppserverIo\Storage\StorageInterface $authenticators The storage instance
     *
     * @return void
     */
    public function injectAuthenticators(StorageInterface $authenticators)
    {
        $this->authenticators = $authenticators;
    }

    /**
     * Inject the map with the realms.
     *
     * @param \AppserverIo\Collections\MapInterface $realms The realms
     *
     * @return void
     */
    public function injectRealms(MapInterface $realms)
    {
        $this->realms = $realms;
    }

    /**
     * Inject the storage for the URL pattern to authenticator mappings.
     *
     * @param \AppserverIo\Storage\StorageInterface $mappings The storage instance
     *
     * @return void
     */
    public function injectMappings(StorageInterface $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Add's the passed realm the the authentication manager.
     *
     * @param \AppserverIo\Psr\Auth\RealmInterface $realm The realm to add
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
     * Register's the passed authenticator.
     *
     * @param \AppserverIo\Psr\Auth\AuthenticatorInterface $authenticator The authenticator to add
     *
     * @return void
     */
    public function addAuthenticator(AuthenticatorInterface $authenticator)
    {
        $this->authenticators->set($authenticator->getSerial(), $authenticator);
    }

    /**
     * Returns the configured authenticator for the passed URL pattern authenticator mapping.
     *
     * @param \AppserverIo\Psr\Auth\MappingInterface|null $mapping The URL pattern to authenticator mapping
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the authentication types
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the authenticator with the passed key is not available
     */
    public function getAuthenticator(MappingInterface $mapping = null)
    {

        // query whether or not a mapping has been passed
        if ($mapping != null) {
            // query whether or not we've an authentication manager with the passed key
            if (isset($this->authenticators[$authenticatorSerial = $mapping->getAuthenticatorSerial()])) {
                return $this->authenticators[$authenticatorSerial];
            }

            // throw an exception if not
            throw new AuthenticationException(sprintf('Can\'t find authenticator serial %s', $authenticatorSerial));
        }

        // try to find the default authenticator instead
        foreach ($this->authenticators as $authenticator) {
            if ($authenticator->isDefaultAuthenticator()) {
                return $authenticator;
            }
        }

        // throw an exception if we can't find a default authenticator also
        throw new AuthenticationException('Can\'t find a default authenticator');
    }

    /**
     * Returns the configured authentication types.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage with the authentication types
     */
    public function getAuthenticators()
    {
        return $this->authenticators;
    }

    /**
     * Register's a new URL pattern to authentication type mapping.
     *
     * @param \AppserverIo\Psr\Auth\MappingInterface $mapping The URL pattern to authenticator mapping
     *
     * @return void
     */
    public function addMapping(MappingInterface $mapping)
    {
        $this->mappings->set($mapping->getUrlPattern(), $mapping);
    }

    /**
     * Return's the storage for the URL pattern to authenticator mappings.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage instance
     */
    public function getMappings()
    {
        return $this->mappings;
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
        /** @var \AppserverIo\Appserver\ServletEngine\Security\MappingInterface $mapping */
        foreach ($this->getMappings() as $mapping) {
            try {
                // query whether or not the URI matches against the URL pattern
                if ($mapping->match($servletRequest)) {
                    // query whether or not the the HTTP method has to be denied or authenticated
                    if (in_array($servletRequest->getMethod(), $mapping->getHttpMethodOmissions())) {
                        // this resource has to be omitted
                        $authenticated = false;

                    } elseif (in_array($servletRequest->getMethod(), $mapping->getHttpMethods())) {
                        // load the authentication method and authenticate the request
                        $authenticator = $this->getAuthenticator($mapping);

                        // if we've an user principal, query the roles
                        if ($authenticator->authenticate($servletRequest, $servletResponse)) {
                            // query whether or not the mapping has roles the user has to be assigned to
                            if (sizeof($mapping->getRoleNames()) === 0) {
                                // if not, we're authenticated
                                return $authenticated;
                            }

                            // initialize the roles flag
                            $inRole = false;

                            // query whether or not the user has at least one of the requested roles
                            foreach ($mapping->getRoleNames() as $role) {
                                if ($servletRequest->isUserInRole(new String($role))) {
                                    $inRole = true;
                                    break;
                                }
                            }

                            // if not, throw an SecurityException
                            if ($inRole === false) {
                                throw new SecurityException(sprintf('User doesn\'t have necessary privileges for resource %s', $servletRequest->getUri()), 403);
                            }
                        }

                    } else {
                        // load the session
                        if ($session = $servletRequest->getSession(true)) {
                            //  start it, if not already done
                            if ($session->isStarted() === false) {
                                $session->start();
                            }

                            // and query whether or not the session contains a user principal
                            if ($session->hasKey(Constants::PRINCIPAL)) {
                                $servletRequest->setUserPrincipal($session->getData(Constants::PRINCIPAL));
                            }
                        }
                    }

                    // stop processing, because we're authenticated
                    break;
                }

            } catch (SecurityException $se) {
                // load the system logger and debug log the exception
                /** @var \Psr\Log\LoggerInterface $systemLogger */
                if ($systemLogger = $this->getApplication()->getNamingDirectory()->search(NamingDirectoryKeys::SYSTEM_LOGGER)) {
                    $systemLogger->error($se->__toString());
                }

                // stop processing, because authentication failed for some reason
                $servletResponse->setStatusCode($se->getCode());
                $servletRequest->setAttribute(RequestHandlerKeys::ERROR_MESSAGE, $se->__toString());
                $servletRequest->setDispatched(true);
                return false;

            } catch (\Exception $e) {
                // load the system logger and debug log the exception
                /** @var \Psr\Log\LoggerInterface $systemLogger */
                if ($systemLogger = $this->getApplication()->getNamingDirectory()->search(NamingDirectoryKeys::SYSTEM_LOGGER)) {
                    $systemLogger->error($e->__toString());
                }

                // stop processing, because authentication failed for some reason
                $servletResponse->setStatusCode(500);
                $servletRequest->setAttribute(RequestHandlerKeys::ERROR_MESSAGE, $e->__toString());
                $servletRequest->setDispatched(true);
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
                $realm = new Realm($this, $securityDomainNode->getName());
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
     * @param string $realmName The name of the requested realm
     *
     * @return object The requested realm instance
     */
    public function getRealm($realmName)
    {
        return $this->getRealms()->get($realmName);
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
