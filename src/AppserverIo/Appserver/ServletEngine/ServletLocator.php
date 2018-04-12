<?php

/**
 * \AppserverIo\Appserver\ServletEngine\ServletLocator
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

use AppserverIo\Psr\Di\ProviderInterface;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Servlet\ServletContextInterface;

/**
 * The servlet resource locator implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServletLocator implements ResourceLocatorInterface
{

    /**
     * Tries to locate the resource related with the request.
     *
     * @param \AppserverIo\Psr\Servlet\ServletContextInterface $servletContext The servlet context that handles the servlets
     * @param string                                           $servletPath    The servlet path to return the servlet for
     *
     * @return \AppserverIo\Psr\Servlet\ServletInterface The requested servlet
     * @throws \AppserverIo\Appserver\ServletEngine\ServletNotFoundException Is thrown if no servlet can be found for the passed request
     * @see \AppserverIo\Appserver\ServletEngine\ServletLocator::locate()
     */
    public function locate(ServletContextInterface $servletContext, $servletPath)
    {

        // iterate over all servlets and return the matching one
        foreach ($servletContext->getServletMappings() as $urlPattern => $servletName) {
            if (fnmatch($urlPattern, $servletPath)) {
                // load the object manager instance
                /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
                $objectManager = $servletContext->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

                // load the provider instance
                /** @var \AppserverIo\Psr\Di\ProviderInterface $provider */
                $provider = $servletContext->getApplication()->search(ProviderInterface::IDENTIFIER);

                // load the object descriptor and re-inject the dependencies
                $provider->injectDependencies(
                    $objectManager->getObjectDescriptor($servletName),
                    $instance = $servletContext->getServlet($servletName)
                );

                // finally return the instance
                return $instance;
            }
        }

        // throw an exception if no servlet matches the servlet path
        throw new ServletNotFoundException(
            sprintf('Can\'t find servlet for requested path "%s"', $servletPath)
        );
    }
}
