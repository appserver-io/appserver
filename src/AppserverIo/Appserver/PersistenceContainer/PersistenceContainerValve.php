<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\PersistenceContainerValve
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Appserver\ServletEngine\ValveInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\RemoteMethodInvocation\RemoteMethodProtocol;
use AppserverIo\RemoteMethodInvocation\RemoteExceptionWrapper;
use AppserverIo\Collections\HashMap;
use AppserverIo\Psr\EnterpriseBeans\BeanContextInterface;

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
class PersistenceContainerValve implements ValveInterface
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

        try {
            // unpack the remote method call
            $remoteMethod = RemoteMethodProtocol::unpack($servletRequest->getBodyContent());

            // load the application context
            /** @var \AppserverIo\Appserver\Application\Application $application */
            $application = $servletRequest->getContext();

            // invoke the remote method and re-attach the bean instance to the container
            $response = $application->search(BeanContextInterface::IDENTIFIER)->invoke($remoteMethod, new HashMap());

            // serialize the remote method and write it to the socket
            $servletResponse->appendBodyStream(RemoteMethodProtocol::pack($response));

        } catch (\Exception $e) {
            // catch the exception and append it to the body stream
            $servletResponse->appendBodyStream(RemoteMethodProtocol::pack(RemoteExceptionWrapper::factory($e)));
        }

        // finally dispatch this request, because we have finished processing it
        $servletRequest->setDispatched(true);
    }
}
