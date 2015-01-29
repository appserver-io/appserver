<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\AbstractAuthentication
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 * @author    Philipp Dittert <pd@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;

/**
 * Abstract class for authentication adapters.
 *
 * @author    Philipp Dittert <pd@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractAuthentication
{

    /**
     * Basic HTTP authentication method.
     *
     * @var string
     */
    const AUTHENTICATION_METHOD_BASIC = 'Basic';

    /**
     * Digest HTTP authentication method.
     *
     * @var string
     */
    const AUTHENTICATION_METHOD_DIGEST = 'Digest';

    /**
     * Holds the Http servlet request instance.
     *
     * @var \AppserverIo\Psr\Servlet\ServletRequestInterface
     */
    protected $servletRequest;

    /**
     * Holds the Http servlet response instance.
     *
     * @var \AppserverIo\Psr\Servlet\ServletResponseInterface
     */
    protected $servletResponse;

    /**
     * The configuration with the secured URLs.
     *
     * @var array
     */
    protected $securedUrlConfig = array();

    /**
     * The configuration with the secured URLs.
     *
     * @param array $securedUrlConfig The secured URL configuration
     */
    public function __construct($securedUrlConfig)
    {
        $this->securedUrlConfig = $securedUrlConfig;
    }

    /**
     * An alternative constructor that has to be called manually.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function init(ServletRequestInterface $servletRequest, ServletResponseInterface $servletResponse)
    {
        $this->setServletRequest($servletRequest);
        $this->setServletResponse($servletResponse);
    }

    /**
     * Sets servlet request instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface $servletRequest The request instance
     *
     * @return void
     */
    protected function setServletRequest(ServletRequestInterface $servletRequest)
    {
        $this->servletRequest = $servletRequest;
    }

    /**
     * Returns servlet request instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletRequestInterface The servlet request instance
     */
    protected function getServletRequest()
    {
        return $this->servletRequest;
    }

    /**
     * Sets servlet response instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    protected function setServletResponse(ServletResponseInterface $servletResponse)
    {
        $this->servletResponse = $servletResponse;
    }

    /**
     * Returns servlet response instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletResponseInterface The servlet response instance
     */
    protected function getServletResponse()
    {
        return $this->servletResponse;
    }

    /**
     * The configuration with the secured URLs.
     *
     * @return array The secured URL configuration
     */
    protected function getSecuredUrlConfig()
    {
        return $this->securedUrlConfig;
    }
}
