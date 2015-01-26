<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\PersistenceContainerValve
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

use AppserverIo\Appserver\ServletEngine\Valve;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use AppserverIo\Psr\EnterpriseBeans\BeanContext;
use AppserverIo\RemoteMethodInvocation\RemoteMethodProtocol;

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
class PersistenceContainerValve implements Valve
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

        try {
            // unpack the remote method call
            $remoteMethod = RemoteMethodProtocol::unpack($servletRequest->getBodyContent());

            // load the application context
            $application = $servletRequest->getContext();

            // prepare method name and parameters and invoke method
            $className = $remoteMethod->getClassName();
            $methodName = $remoteMethod->getMethodName();
            $parameters = $remoteMethod->getParameters();
            $sessionId = $remoteMethod->getSessionId();

            // load the bean manager and the bean instance
            $beanManager = $application->search('BeanContext');
            $instance = $application->search($className, array($sessionId, array($application)));

            // invoke the remote method call on the local instance
            $response = call_user_func_array(array($instance, $methodName), $parameters);

            // serialize the remote method and write it to the socket
            $servletResponse->appendBodyStream(RemoteMethodProtocol::pack($response));

            // reattach the bean instance in the container and unlock it
            $beanManager->attach($instance, $sessionId);

        } catch (\Exception $e) {
            // catch the exception and append it to the body stream
            $servletResponse->appendBodyStream(RemoteMethodProtocol::pack($e));
        }

        // finally dispatch this request, because we have finished processing it
        $servletRequest->setDispatched(true);
    }
}
