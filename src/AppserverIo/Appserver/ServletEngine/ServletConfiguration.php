<?php

/**
 * \AppserverIo\Appserver\ServletEngine\ServletConfiguration
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

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Psr\Servlet\ServletConfigInterface;

/**
 * Servlet configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServletConfiguration implements ServletConfigInterface
{

    /**
     * The servlets name from the web.xml configuration file.
     *
     * @var string
     */
    protected $servletName;

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Servlet\ServletContextInterface
     */
    protected $servletContext;

    /**
     * Array with the servlet's init parameters found in the web.xml configuration file.
     *
     * @var array
     */
    protected $initParameter = array();

    /**
     * Initializes the servlet configuration with the servlet context instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletContextInterface $servletContext The servlet context instance
     *
     * @return void
     */
    public function injectServletContext($servletContext)
    {
        $this->servletContext = $servletContext;
    }

    /**
     * Sets the servlet's Uname from the web.xml configuration file.
     *
     * @param string $servletName The servlet name
     *
     * @return void
     */
    public function injectServletName($servletName)
    {
        $this->servletName = $servletName;
    }

    /**
     * Returns the servlet context instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletContextInterface The servlet context instance
     */
    public function getServletContext()
    {
        return $this->servletContext;
    }

    /**
     * Returns the servlet's name from the web.xml configuration file.
     *
     * @return string The servlet name
     * @see \AppserverIo\Psr\Servlet\ServletConfigInterface::getServletName()
     */
    public function getServletName()
    {
        return $this->servletName;
    }

    /**
     * Returns the path to the appserver webapp base directory.
     *
     * @return string The path to the appserver webapp base directory
     */
    public function getAppBase()
    {
        return $this->getServletContext()->getAppBase();
    }

    /**
     * Returns the webapp base path.
     *
     * @return string The webapp base path
     */
    public function getWebappPath()
    {
        return $this->getServletContext()->getWebappPath();
    }

    /**
     * Register's the init parameter under the passed name.
     *
     * @param string $name  Name to register the init parameter with
     * @param string $value The value of the init parameter
     *
     * @return void
     */
    public function addInitParameter($name, $value)
    {
        $this->initParameter[$name] = $value;
    }

    /**
     * Return's the init parameter with the passed name.
     *
     * @param string $name Name of the init parameter to return
     *
     * @return string
     */
    public function getInitParameter($name)
    {
        if (array_key_exists($name, $this->initParameter)) {
            return $this->initParameter[$name];
        }
    }
}
