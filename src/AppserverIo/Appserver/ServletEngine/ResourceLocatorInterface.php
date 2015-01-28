<?php

/**
 * AppserverIo\Appserver\ServletEngine\ResourceLocatorInterface
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

use AppserverIo\Psr\Servlet\ServletContextInterface;

/**
 * Interface for the resource locator instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ResourceLocatorInterface
{

    /**
     * Tries to locate the resource related with the request.
     *
     * @param \AppserverIo\Psr\Servlet\ServletContextInterface $servletContext The servlet context that handles the servlets
     * @param string                                           $servletPath    The servlet path to return the servlet for
     *
     * @return \AppserverIo\Psr\Servlet\ServletInterface The requested servlet
     */
    public function locate(ServletContextInterface $servletContext, $servletPath);
}
