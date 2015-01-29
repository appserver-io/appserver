<?php

/**
 * AppserverIo\Appserver\ServletEngine\ServletValve
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

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * Valve implementation that will be executed by the servlet engine to handle
 * an incoming HTTP servlet request.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServletValve implements ValveInterface
{

    /**
     * Processes the request by invoking the request handler that executes the servlet
     * in a protected context.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function invoke(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // load the servlet manager
        $servletManager = $servletRequest->getContext()->search('ServletContextInterface');

        // locate and service the servlet
        $servlet = $servletManager->locate($servletRequest);
        $servlet->service($servletRequest, $servletResponse);

        // finally invoke the destroy() method
        $servlet->destroy();

        // dispatch this request, because we have finished processing it
        $servletRequest->setDispatched(true);
    }
}
