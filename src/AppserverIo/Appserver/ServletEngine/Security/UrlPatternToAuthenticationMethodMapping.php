<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Security\UrlPatternToAuthenticationMethodMapping
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

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;

/**
 * The mapping class to map an URL pattern to an authentication method.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class UrlPatternToAuthenticationMethodMapping
{

    /**
     * Initialize the mapping with the passed values.
     *
     * @param string $urlPattern              The URL pattern
     * @param string $authenticationMethodKey The authentication method's key
     * @param array  $httpMethods             The array with the HTTP methods that has to be authenticated
     * @param array  $httpMethodOmissions     The array with the HTTP methods that has to be omissed from authentication
     */
    public function __construct($urlPattern, $authenticationMethodKey, array $httpMethods = array(), array $httpMethodOmissions = array())
    {
        $this->urlPattern = $urlPattern;
        $this->authenticationMethodKey = $authenticationMethodKey;
        $this->httpMethods = $httpMethods;
        $this->httpMethodOmissions = $httpMethodOmissions;
    }

    /**
     * Return's the URL pattern.
     *
     * @return string The URL pattern
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    /**
     * Return's the authentication method's key.
     *
     * @return string The key
     */
    public function getAuthenticationMethodKey()
    {
        return $this->authenticationMethodKey;
    }

    /**
     * Return's the HTTP methods that has to be authenticated.
     *
     * @return array The HTTP methods
     */
    public function getHttpMethods()
    {
        return $this->httpMethods;
    }

    /**
     * Return's the HTTP methods that has to b omissed from authentication
     *
     * @return array The HTTP methods
     */
    public function getHttpMethodOmissions()
    {
        return $this->httpMethodOmissions;
    }

    /**
     * Return's TRUE if the passed request matches the mappings URL patter.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The request to match
     *
     * @return boolean TRUE if the request matches, else FALSE
     */
    public function match(HttpServletRequestInterface $servletRequest)
    {
        return fnmatch($this->getUrlPattern(), $servletRequest->getServletPath() . $servletRequest->getPathInfo());
    }
}
