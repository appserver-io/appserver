<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Security\DependencyInjection\DeploymentDescriptorParser
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

namespace AppserverIo\Appserver\ServletEngine\Security\DependencyInjection;

use AppserverIo\Lang\Boolean;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Appserver\Core\Api\Node\WebAppNode;
use AppserverIo\Appserver\ServletEngine\Security\Mapping;
use AppserverIo\Psr\Auth\AuthenticationManagerInterface;
use AppserverIo\Http\Authentication\AuthenticationException;

/**
 * Parser implementation to parse a web application deployment descriptor (WEB-INF/web.xml).
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DeploymentDescriptorParser
{

    /**
     * The servlet context we want to parse the deployment descriptor for.
     *
     * @var \AppserverIo\Psr\Servlet\ServletContextInterface
     */
    protected $authenticationContext;

    /**
     * Inject the authentication context instance.
     *
     * @param \AppserverIo\Psr\Auth\AuthenticationManagerInterface $authenticationContext The authentication context instance
     *
     * @return void
     */
    public function injectAuthenticationContext(AuthenticationManagerInterface $authenticationContext)
    {
        $this->authenticationContext = $authenticationContext;
    }

    /**
     * Returns the servlet context instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletContextInterface The servlet context instance
     */
    public function getAuthenticationContext()
    {
        return $this->authenticationContext;
    }

    /**
     * Returns the application context instance the servlet context is bound to.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application context instance
     */
    public function getApplication()
    {
        return $this->getAuthenticationContext()->getApplication();
    }

    /**
     * Returns the authenticator class name for the passed shortname.
     *
     * @param string $shortname The shortname of the requested authenticator class name
     *
     * @return string The requested authenticator class name
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if no mapping for the requested authenticator is not available
     */
    public function mapAuthenticator($shortname)
    {

        // query whether or not we've manager configuration found
        /** @var \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerNode */
        if ($managerNode = $this->getAuthenticationContext()->getManagerConfiguration()) {
            // initialize the authenticator configurations found in the manager configuration
            /** @var \AppserverIo\Appserver\Core\Api\Node\AuthenticatorNodeInterface $authenticatorNode */
            foreach ($managerNode->getAuthenticators() as $authenticatorNode) {
                // query whether or not the shortname matches
                if (strcasecmp($authenticatorNode->getName(), $shortname) === 0) {
                    return $authenticatorNode->getType();
                }
            }
        }

        // throw an exception if the can't find an matching authenticator class name
        throw new AuthenticationException(sprintf('Can\t find authenticator configuration for %s', $shortname));
    }

    /**
     * Parses the servlet context's deployment descriptor file for servlets
     * that has to be registered in the object manager.
     *
     * @return void
     */
    public function parse()
    {

        // load the web application base directory
        $webappPath = $this->getAuthenticationContext()->getWebappPath();

        // prepare the deployment descriptor
        $deploymentDescriptor = $webappPath . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'web.xml';

        // query whether we found epb.xml deployment descriptor file
        if (file_exists($deploymentDescriptor) === false) {
            return;
        }

        // validate the passed configuration file
        /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
        $configurationService = $this->getApplication()->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
        $configurationService->validateFile($deploymentDescriptor, null, true);

        // prepare and initialize the configuration node
        $webAppNode = new WebAppNode();
        $webAppNode->initFromFile($deploymentDescriptor);

        // query whether or not we've a login configuration
            /** @var \AppserverIo\Appserver\Core\Api\Node\LoginConfigNode $loginConfig */
        if ($loginConfig = $webAppNode->getLoginConfig()) {
            // create the authentication method instance
            $reflectionClass = new ReflectionClass($this->mapAuthenticator($loginConfig->getAuthMethod()->__toString()));
            $authenticator = $reflectionClass->newInstanceArgs(array($loginConfig, $this->getAuthenticationContext(), new Boolean(true)));

            // add the authentication method itself
            $this->getAuthenticationContext()->addAuthenticator($authenticator);

            // initialize the security roles, that are part of the new security subsystem
            /** @var \AppserverIo\Appserver\Core\Api\Node\SecurityRoleNode $securityRoleNode */
            foreach ($webAppNode->getSecurityRoles() as $securityRoleNode) {
                // do something here
            }

            // initialize the security roles, that are part of the new security subsystem
            /** @var \AppserverIo\Appserver\Core\Api\Node\SecurityConstraintNode $securityContstraintNode */
            foreach ($webAppNode->getSecurityConstraints() as $securityContstraintNode) {
                // prepare the array with the authentication constraint role names
                $roleNames = array();
                if ($authConstraint = $securityContstraintNode->getAuthConstraint()) {
                    $roleNames = $authConstraint->getRoleNamesAsArray();
                }
                /** @var \AppserverIo\Appserver\Core\Api\Node\WebResourceCollectionNode $webResourceCollectionNode */
                foreach ($securityContstraintNode->getWebResourceCollections() as $webResourceCollectionNode) {
                    // prepare the arrays for the HTTP methods and the method omissions
                    $httpMethods = $webResourceCollectionNode->getHttpMethodsAsArray();
                    $httpMethodOmissions = $webResourceCollectionNode->getHttpMethodOmissionsAsArray();
                    /** @var \AppserverIo\Appserver\Core\Api\Node\UrlPatternNode $urlPatternNode */
                    foreach ($webResourceCollectionNode->getUrlPatterns() as $urlPatternNode) {
                        // prepare the URL pattern to authenticator mapping with the necessary data
                        $mapping = new Mapping(
                            $urlPatternNode->__toString(),
                            $authenticator->getSerial(),
                            $roleNames,
                            $httpMethods,
                            $httpMethodOmissions
                        );
                        // add the URL pattern to authenticator mapping
                        $this->getAuthenticationContext()->addMapping($mapping);
                    }
                }
            }
        }
    }
}
