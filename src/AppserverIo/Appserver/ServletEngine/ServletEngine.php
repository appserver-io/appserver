<?php

/**
 * AppserverIo\Appserver\ServletEngine\ServletEngine
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

use AppserverIo\Http\HttpCookie;
use AppserverIo\Http\HttpProtocol;
use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Application\VirtualHost;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;
use AppserverIo\Psr\Servlet\ServletRequest;
use AppserverIo\Psr\Servlet\ServletResponse;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use AppserverIo\Server\Dictionaries\ModuleHooks;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\WebServer\Interfaces\HttpModuleInterface;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Exceptions\ModuleException;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Appserver\ServletEngine\Http\Request;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Appserver\ServletEngine\Http\Part;
use AppserverIo\Appserver\ServletEngine\BadRequestException;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationValve;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use AppserverIo\Server\Dictionaries\EnvVars;
use AppserverIo\Psr\HttpMessage\CookieInterface;

/**
 * A servlet engine implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ServletEngine extends AbstractServletEngine
{

    /**
     * The unique module name in the web server context.
     *
     * @var string
     */
    const MODULE_NAME = 'servlet';

    /**
     * Timeout to wait for a free request handler: 1 s
     *
     * @var integer
     */
    const REQUEST_HANDLER_WAIT_TIMEOUT = 1000000;

    /**
     * Returns the module name.
     *
     * @return string The module name
     */
    public function getModuleName()
    {
        return ServletEngine::MODULE_NAME;
    }

    /**
     * Initializes the module.
     *
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext The servers context instance
     *
     * @return void
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function init(ServerContextInterface $serverContext)
    {
        try {

            // set the servlet context
            $this->serverContext = $serverContext;

            // initialize the servlet engine
            $this->initValves();
            $this->initHandlers();
            $this->initVirtualHosts();
            $this->initApplications();
            $this->initUrlMappings();

        } catch (\Exception $e) {
            throw new ModuleException($e);
        }
    }

    /**
     * Process servlet request.
     *
     * @param \AppserverIo\Psr\HttpMessage\RequestInterface          $request        A request object
     * @param \AppserverIo\Psr\HttpMessage\ResponseInterface         $response       A response object
     * @param \AppserverIo\Server\Interfaces\RequestContextInterface $requestContext A requests context instance
     * @param int                                                    $hook           The current hook to process logic for
     *
     * @return bool
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function process(
        RequestInterface $request,
        ResponseInterface $response,
        RequestContextInterface $requestContext,
        $hook
    ) {

        try {

            // if false hook is comming do nothing
            if (ModuleHooks::REQUEST_POST !== $hook) {
                return;
            }

            // check if we are the handler that has to process this request
            if ($requestContext->getServerVar(ServerVars::SERVER_HANDLER) !== $this->getModuleName()) {
                return;
            }

            // intialize servlet session, request + response
            $servletRequest = new Request();
            $servletRequest->injectHttpRequest($request);
            $servletRequest->injectServerVars($requestContext->getServerVars());

            // initialize the parts
            foreach ($request->getParts() as $name => $part) {
                $servletRequest->addPart(Part::fromHttpRequest($part));
            }

            // set the body content if we can find one
            if ($request->getHeader(HttpProtocol::HEADER_CONTENT_LENGTH) > 0) {
                $servletRequest->setBodyStream($request->getBodyContent());
            }

            // prepare the servlet request
            $this->prepareServletRequest($servletRequest);

            // initialize the servlet response with the Http response values
            $servletResponse = new Response();
            $servletRequest->injectResponse($servletResponse);

            // load a NOT working request handler from the pool
            $valves = $this->valves;
            $urlMappings = $this->urlMappings;
            $applications = $this->applications;

            // explode host and port from the host header
            list ($host, ) = explode(':', $request->getHeader(HttpProtocol::HEADER_HOST));

            // prepare the request URL we want to match
            $url =  $host . $requestContext->getServerVar(ServerVars::X_REQUEST_URI);

            // try to match a registered application with the passed request
            foreach ($urlMappings as $pattern => $applicationName) {
                if (preg_match($pattern, $url) === 1) {
                    break;
                }
            }

            // check if an application is available
            if (isset($applications[$applicationName]) === false) { // if not throw a bad request exception
                throw new BadRequestException(sprintf('Can\'t find application for URL %s', $url));
            }

            // load the application
            $application = $applications[$applicationName];

            // initialize the request handler instance
            $requestHandler = new RequestHandler();
            $requestHandler->injectValves($valves);
            $requestHandler->injectApplication($application);
            $requestHandler->injectRequest($servletRequest);
            $requestHandler->injectResponse($servletResponse);
            $requestHandler->start();
            $requestHandler->join();

            // copy the values from the servlet response back to the HTTP response
            $response->setStatusCode($servletResponse->getStatusCode());
            $response->setStatusReasonPhrase($servletResponse->getStatusReasonPhrase());
            $response->setVersion($servletResponse->getVersion());
            $response->setState($servletResponse->getState());

            // append the content to the body stream
            $response->appendBodyStream($servletResponse->getBodyStream());

            // transform the servlet headers back into HTTP headers
            $headers = array();
            foreach ($servletResponse->getHeaders() as $name => $header) {
                $headers[$name] = $header;
            }

            // set the headers as array (because we don't know if we have to use the append flag)
            $response->setHeaders($headers);

            // copy the servlet response cookies back to the HTTP response
            foreach ($servletResponse->getCookies() as $cookieName => $cookieValue) {

                // load the cookie and check if we've an array or a single cookie instance
                if (is_array($cookie = $servletResponse->getCookie($cookieName))) {

                    foreach ($cookie as $c) { // add all the cookies
                        $response->addCookie($c);
                    }

                } else { // add the cookie instance directly
                    $response->addCookie($cookie);
                }
            }

            // set response state to be dispatched after this without calling other modules process
            $response->setState(HttpResponseStates::DISPATCH);

        } catch (ModuleException $me) {
            throw $me;
        } catch (\Exception $e) {
            throw new ModuleException($e, 500);
        }
    }

    /**
     * Tries to find a request handler that matches the actual request and injects it into the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest $servletRequest The servlet request we need a request handler to handle for
     *
     * @return string The application name of the application to handle the request
     * @deprecated This method is deprecated since 0.8.0
     */
    protected function requestHandlerFromPool(HttpServletRequest $servletRequest)
    {
        // nothing to do here
    }

    /**
     * After a request has been processed by the injected request handler we remove
     * the thread ID of the request handler from the array with the working handlers.
     *
     * @param \AppserverIo\Appserver\ServletEngine\RequestHandler $requestHandler The request handler instance we want to re-attach to the pool
     *
     * @return void
     * @deprecated This method is deprecated since 0.8.0
     */
    protected function requestHandlerToPool($requestHandler)
    {
        // nothing to do here
    }
}
