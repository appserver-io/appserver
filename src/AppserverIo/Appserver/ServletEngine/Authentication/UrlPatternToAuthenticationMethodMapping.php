<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Authentication\UrlPatternToAuthenticationMethodMapping
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

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class UrlPatternToAuthenticationMethodMapping
{

    public function __construct($urlPattern, $authenticationMethodKey, array $httpMethods = array(), array $httpMethodOmissions = array())
    {
        $this->urlPattern = $urlPattern;
        $this->authenticationMethodKey = $authenticationMethodKey;
        $this->httpMethods = $httpMethods;
        $this->httpMethodOmissions = $httpMethodOmissions;
    }

    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    public function getAuthenticationMethodKey()
    {
        return $this->authenticationMethodKey;
    }

    public function getHttpMethods()
    {
        return $this->httpMethods;
    }

    public function getHttpMethodOmissions()
    {
        return $this->httpMethodOmissions;
    }

    public function match(HttpServletRequestInterface $servletRequest)
    {
        return fnmatch($this->getUrlPattern(), $servletRequest->getServletPath() . $servletRequest->getPathInfo());
    }
}
