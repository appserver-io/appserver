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
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Philipp Dittert <pd@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Psr\Servlet\Servlet;
use AppserverIo\Psr\Servlet\ServletRequest;
use AppserverIo\Psr\Servlet\ServletResponse;

/**
 * Abstract class for authentication adapters.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Philipp Dittert <pd@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
     * @var \AppserverIo\Psr\Servlet\HttpServletRequest
     */
    protected $servletRequest;

    /**
     * Holds the Http servlet response instance.
     *
     * @var \AppserverIo\Psr\Servlet\ServletResponse
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
     * @param \AppserverIo\Psr\Servlet\ServletRequest  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function init(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
        $this->setServletRequest($servletRequest);
        $this->setServletResponse($servletResponse);
    }

    /**
     * Sets servlet request instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequest $servletRequest The request instance
     *
     * @return void
     */
    protected function setServletRequest(ServletRequest $servletRequest)
    {
        $this->servletRequest = $servletRequest;
    }

    /**
     * Returns servlet request instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletRequest The servlet request instance
     */
    protected function getServletRequest()
    {
        return $this->servletRequest;
    }

    /**
     * Sets servlet response instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletResponse $servletResponse The response instance
     *
     * @return void
     */
    protected function setServletResponse(ServletResponse $servletResponse)
    {
        $this->servletResponse = $servletResponse;
    }

    /**
     * Returns servlet response instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletResponse The servlet response instance
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
