<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Authentication\DependencyInjection\DeploymentDescriptorParser
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

namespace AppserverIo\Appserver\ServletEngine\Authentication\DependencyInjection;

use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Appserver\Core\Api\Node\WebAppNode;
use AppserverIo\Appserver\Core\Api\Node\WebAppNodeInterface;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface;
use AppserverIo\Appserver\ServletEngine\Authentication\FormAuthentication;
use AppserverIo\Appserver\ServletEngine\Authentication\BasicAuthentication;
use AppserverIo\Appserver\ServletEngine\Authentication\DigestAuthentication;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Appserver\ServletEngine\Authentication\UrlPatternToAuthenticationMethodMapping;

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
     * The available authentication types.
     *
     * @var string
     */
    protected $authenticationTypes = array(
        'Form'   => '\AppserverIo\Appserver\ServletEngine\Authentication\FormAuthentication',
        'Basic'  => '\AppserverIo\Appserver\ServletEngine\Authentication\BasicAuthentication',
        'Digest' => '\AppserverIo\Appserver\ServletEngine\Authentication\DigestAuthentication'
    );

    /**
     * The servlet context we want to parse the deployment descriptor for.
     *
     * @var \AppserverIo\Psr\Servlet\ServletContextInterface
     */
    protected $authenticationContext;

    /**
     * Inject the authentication context instance.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface $authenticationContext The authentication context instance
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
     * Returns the authentication type class name for the passed shortname.
     *
     * @param string $shortname The shortname of the requested authentication type class name
     *
     * @return string The requested authentication type class name
     * @throws ConfigurationException
     */
    public function mapAuthenticationType($shortname)
    {

        // query whether or not an authentication type is available or not
        if (isset($this->authenticationTypes[$shortname])) {
            return $this->authenticationTypes[$shortname];
        }

        // throw an exception if the can't find an matching authentication type
        throw new AuthenticationException(sprintf('Can\t find authentication type %s', $shortname));
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

        // initialize the old security subsystem
        $this->processSecurities($webAppNode);

        // query whether or not we've a login configuration
            /** @var \AppserverIo\Appserver\Core\Api\Node\LoginConfigNode $loginConfig */
        if ($loginConfig = $webAppNode->getLoginConfig()) {

            // create the authentication method instance
            $reflectionClass = new ReflectionClass($this->mapAuthenticationType($loginConfig->getAuthMethod()->__toString()));
            $authenticationMethod = $reflectionClass->newInstanceArgs(array($loginConfig));

            // add the authentication method itself
            $this->getAuthenticationContext()->addAuthenticationMethod($loginConfig->getPrimaryKey(), $authenticationMethod);

            // initialize the security roles, that are part of the new security subsystem
            /** @var \AppserverIo\Appserver\Core\Api\Node\SecurityRoleNode $securityRoleNode */
            foreach ($webAppNode->getSecurityRoles() as $securityRoleNode) {
                // do something here
            }

            // initialize the security roles, that are part of the new security subsystem
            /** @var \AppserverIo\Appserver\Core\Api\Node\SecurityConstraintNode $securityContstraintNode */
            foreach ($webAppNode->getSecurityConstraints() as $securityContstraintNode) {
                /** @var \AppserverIo\Appserver\Core\Api\Node\WebResourceCollectionNode $webResourceCollectionNode */
                foreach ($securityContstraintNode->getWebResourceCollections() as $webResourceCollectionNode) {
                    /** @var \AppserverIo\Appserver\Core\Api\Node\UrlPatternNode $urlPatternNode */
                    foreach ($webResourceCollectionNode->getUrlPatterns() as $urlPatternNode) {
                        // prepare the URL pattern to authentication method mapping with the necessary data
                        $urlPatternToAuthenticationMethodMapping = new UrlPatternToAuthenticationMethodMapping(
                            $urlPatternNode->__toString(),
                            $loginConfig->getPrimaryKey(),
                            $webResourceCollectionNode->getHttpMethodsAsArray(),
                            $webResourceCollectionNode->getHttpMethodOmissionsAsArray()
                        );

                        // add the URL pattern to authentication method mapping
                        $this->getAuthenticationContext()->addUrlPatternToAuthenticationMethodMapping($urlPatternToAuthenticationMethodMapping);
                    }
                }
            }
        }
    }

    /**
     * Process the old security subsystem configuration and adds the authentication adapters
     * to the authentication manager.
     *
     * @param AppserverIo\Appserver\Core\Api\Node\WebAppNodeInterface $webAppNode The configuration node
     *
     * @return void
     */
    public function processSecurities(WebAppNodeInterface $webAppNode)
    {

        // initialize the old security system, this will be deprecated up from version 1.2
        /** @var \AppserverIo\Appserver\Core\Api\Node\SecurityNode $securityNode */
        foreach ($webAppNode->getSecurities() as $securityNode) {
            // load the authentication type informations
            $urlPattern = $securityNode->getUrlPattern()->__toString();
            $configuredAuthType = $securityNode->getAuth()->getAuthType()->__toString();
            $authImplementation = $securityNode->mapAuthenticationType($configuredAuthType);

            // initialize the authentication manager
            /** @var \AppserverIo\Http\Authentication\AuthenticationInterface $auth */
            $authenticationMethod = new $authImplementation($securityNode->getOptionsAsArray($this->getAuthenticationContext()->getWebappPath()));

            // prepare the URL pattern to authentication method mapping with the necessary data
            $urlPatternToAuthenticationMethodMapping = new UrlPatternToAuthenticationMethodMapping($urlPattern, $securityNode->getPrimaryKey());

            // add the authentication method and URL pattern mapping
            $this->getAuthenticationContext()->addAuthenticationMethod($securityNode->getPrimaryKey(), $authenticationMethod);
            $this->getAuthenticationContext()->addUrlPatternToAuthenticationMethodMapping($urlPatternToAuthenticationMethodMapping);
        }
    }
}
