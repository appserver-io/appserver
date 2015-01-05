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
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Psr\Servlet\ServletContext;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use AppserverIo\Appserver\ServletEngine\Valve;

/**
 * Valve implementation that will be executed by the servlet engine to handle
 * an incoming HTTP servlet request.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ServletValve implements Valve
{

    /**
     * Processes the request by invoking the request handler that executes the servlet
     * in a protected context.
     *
     * @param \AppserverIo\Psr\Servlet\ServletRequest  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\ServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function invoke(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // load the servlet manager
        $servletManager = $servletRequest->getContext()->search('ServletContext');

        // locate and service the servlet
        $servlet = $servletManager->locate($servletRequest);
        $servlet->service($servletRequest, $servletResponse);

        // finally invoke the destroy() method
        $servlet->destroy();

        // dispatch this request, because we have finished processing it
        $servletRequest->setDispatched(true);
    }
}
