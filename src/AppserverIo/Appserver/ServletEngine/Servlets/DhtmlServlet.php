<?php

/**
 * AppserverIo\Appserver\ServletEngine\Servlets\DhtmlServlet
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

namespace AppserverIo\Appserver\ServletEngine\Servlets;

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Psr\Servlet\Http\HttpServlet;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\ServletConfigInterface;
use AppserverIo\Psr\Servlet\ServletRequestInterface;
use AppserverIo\Psr\Servlet\ServletResponseInterface;

/**
 * A simple implementation of a servlet that executes DHTML files that has
 * to be specified as servlet path.
 *
 * The DHTML files are processed in the scope of the service() method. This
 * gives developers access to all variables in the method's scope as to all
 * members of the servlet.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DhtmlServlet extends HttpServlet
{

    /**
     * The string for the X-POWERED-BY header.
     *
     * @var string
     */
    protected $poweredBy;

    /**
     * The path to the actual web application -> the base directory for DHTML templates.
     *
     * @var string
     */
    protected $webappPath;

    /**
     * The path to the application base directory.
     *
     * @var string
     */
    protected $appBase;

    /**
     * The path to the application server's base directory.
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * Returns the string for the X-POWERED-BY header.
     *
     * @return string The X-POWERED-BY header
     */
    public function getPoweredBy()
    {
        return $this->poweredBy;
    }

    /**
     * Returns the path to the web application.
     *
     * @return string The path to the web application
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Returns the path to the application base directory.
     *
     * @return string The application base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * Returns the absolute path to the base directory.
     *
     * @return string The base directory
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Initializes the servlet with the passed configuration.
     *
     * @param \AppserverIo\Psr\Servlet\ServletConfigInterface $servletConfig The configuration to initialize the servlet with
     *
     * @return void
     */
    public function init(ServletConfigInterface $servletConfig)
    {

        // pre-initialize the X-POWERED-BY header
        $this->poweredBy = get_class($this);

        // pre-initialize the possible DHTML template paths
        $this->webappPath = $servletConfig->getWebappPath();
        $this->appBase = $servletConfig->getServletContext()->getAppBase();
        $this->baseDirectory = $servletConfig->getServletContext()->getBaseDirectory();
    }

    /**
     * Processes the DHTML file specified as servlet name.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponseInterface $servletResponse The response sent back to the client
     *
     * @return void
     *
     * @throws \AppserverIo\Psr\Servlet\ServletException If no action has been found for the requested path
     */
    public function service(ServletRequestInterface $servletRequest, ServletResponseInterface $servletResponse)
    {

        // pre-initialize the X-POWERED-BY header
        $poweredBy = $this->getPoweredBy();

        // append an existing X-POWERED-BY header if available
        if ($servletResponse->hasHeader(HttpProtocol::HEADER_X_POWERED_BY)) {
            $poweredBy = $servletResponse->getHeader(HttpProtocol::HEADER_X_POWERED_BY) . ', ' . $poweredBy;
        }

        // set the X-POWERED-BY header
        $servletResponse->addHeader(HttpProtocol::HEADER_X_POWERED_BY, $poweredBy);

        // servlet path === relative path to the template name
        $template = $servletRequest->getServletPath();

        // check if the template is available
        if (!file_exists($pathToTemplate = $this->getWebappPath() . $template)) {
            if (!file_exists($pathToTemplate = $this->getAppBase() . $template)) {
                if (!file_exists($pathToTemplate = $this->getBaseDirectory() . $template)) {
                    throw new ServletException(sprintf('Can\'t load requested template \'%s\'', $template));
                }
            }
        }

        // process the template
        ob_start();
        require $pathToTemplate;

        // add the servlet name to the response
        $servletResponse->appendBodyStream(ob_get_clean());
    }
}
