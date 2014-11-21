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
use AppserverIo\Http\HttpRequestInterface;
use AppserverIo\Http\HttpResponseInterface;
use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Application\VirtualHost;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;
use AppserverIo\Psr\Servlet\ServletRequest;
use AppserverIo\Psr\Servlet\ServletResponse;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use TechDivision\Server\Dictionaries\ModuleHooks;
use TechDivision\Server\Dictionaries\ServerVars;
use TechDivision\Server\Interfaces\ModuleInterface;
use TechDivision\Server\Interfaces\RequestContextInterface;
use TechDivision\Server\Interfaces\ServerContextInterface;
use TechDivision\Server\Exceptions\ModuleException;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Appserver\ServletEngine\Http\Request;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Appserver\ServletEngine\Http\Part;
use AppserverIo\Appserver\ServletEngine\BadRequestException;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationValve;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use TechDivision\Connection\ConnectionRequestInterface;
use TechDivision\Connection\ConnectionResponseInterface;
use TechDivision\Server\Dictionaries\EnvVars;

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
class ServletEngine extends GenericStackable implements ModuleInterface
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
     * Initialize the module.
     *
     * @return void
     */
    public function __construct()
    {

        // initialize the members
        $this->valves = new GenericStackable();
        $this->handlers = new GenericStackable();
        $this->applications = new GenericStackable();
        $this->dependencies = new GenericStackable();
        $this->virtualHosts = new GenericStackable();
        $this->urlMappings = new GenericStackable();
    }

    /**
     * Returns an array of module names which should be executed first.
     *
     * @return \AppserverIo\Storage\GenericStackable The module names this module depends on
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

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
     * @param \TechDivision\Server\Interfaces\ServerContextInterface $serverContext The servers context instance
     *
     * @return void
     * @throws \TechDivision\Server\Exceptions\ModuleException
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
     * Initialize the valves that handles the requests.
     *
     * @return void
     */
    public function initValves()
    {
        $this->valves[] = new AuthenticationValve();
        $this->valves[] = new ServletValve();
    }

    /**
     * Initialize the web server handlers.
     *
     * @return void
     */
    public function initHandlers()
    {
        foreach ($this->getServerContext()->getServerConfig()->getHandlers() as $extension => $handler) {
            $this->handlers[$extension] = new Handler($handler['name']);
        }
    }

    /**
     * Initialize the configured virtual hosts.
     *
     * @return void
     */
    public function initVirtualHosts()
    {
        // load the document root and the web servers virtual host configuration
        $documentRoot = $this->getServerContext()->getServerConfig()->getDocumentRoot();

        // prepare the virtual host configurations
        foreach ($this->getServerContext()->getServerConfig()->getVirtualHosts() as $domain => $virtualHost) {

            // prepare the applications base directory
            $appBase = str_replace($documentRoot, '', $virtualHost['params']['documentRoot']);

            // append the virtual host to the array
            $this->virtualHosts[] = new VirtualHost($domain, $appBase);
        }
    }

    /**
     * Initialize the applications.
     *
     * @return void
     */
    public function initApplications()
    {

        // iterate over a applications vhost/alias configuration
        foreach ($this->getServerContext()->getContainer()->getApplications() as $applicationName => $application) {

            // iterate over the virtual hosts
            foreach ($this->virtualHosts as $virtualHost) {
                if ($virtualHost->match($application)) {
                    $application->addVirtualHost($virtualHost);
                }
            }

            // finally APPEND a wildcard pattern for each application to the patterns array
            $this->applications[$applicationName] = $application;
        }
    }

    /**
     * Initialize the URL mappings.
     *
     * @return void
     */
    public function initUrlMappings()
    {

        // iterate over a applications vhost/alias configuration
        foreach ($this->getApplications() as $application) {

            // initialize the application name
            $applicationName = $application->getName();

            // iterate over the virtual hosts and add a mapping for each
            foreach ($application->getVirtualHosts() as $virtualHost) {
                $this->urlMappings['/^' . $virtualHost->getName() . '\/(([a-z0-9+\$_-]\.?)+)*\/?/'] = $applicationName;
            }

            // finally APPEND a wildcard pattern for each application to the patterns array
            $this->urlMappings['/^[a-z0-9-.]*\/' . $applicationName . '\/(([a-z0-9+\$_-]\.?)+)*\/?/'] = $applicationName;
        }
    }

    /**
     * Prepares the module for upcoming request in specific context
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function prepare()
    {
    }

    /**
     * Process servlet request.
     *
     * @param \TechDivision\Connection\ConnectionRequestInterface     $request        A request object
     * @param \TechDivision\Connection\ConnectionResponseInterface    $response       A response object
     * @param \TechDivision\Server\Interfaces\RequestContextInterface $requestContext A requests context instance
     * @param int                                                     $hook           The current hook to process logic for
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function process(ConnectionRequestInterface $request, ConnectionResponseInterface $response, RequestContextInterface $requestContext, $hook)
    {

        try {

            // In php an interface is, by definition, a fixed contract. It is immutable.
            // So we have to declair the right ones afterwards...
            /** @var $request \AppserverIo\Http\HttpRequestInterface */
            /** @var $request \AppserverIo\Http\HttpResponseInterface */

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
            foreach ($servletResponse->getCookies() as $cookie) {
                $response->addCookie(unserialize($cookie));
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

    /**
     * Tries to find an application that matches the passed request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest $servletRequest The request instance to locate the application for
     *
     * @return array The application info that matches the request
     * @throws \AppserverIo\Appserver\ServletEngine\BadRequestException Is thrown if no application matches the request
     */
    protected function prepareServletRequest(HttpServletRequest $servletRequest)
    {
        // load the request URI and query string
        $uri = $servletRequest->getUri();
        $queryString = $servletRequest->getQueryString();

        // get uri without querystring
        $uriWithoutQueryString = str_replace('?' . $queryString, '', $uri);

        // initialize the path information and the directory to start with
        list ($dirname, $basename, $extension) = array_values(pathinfo($uriWithoutQueryString));

        // make the registered handlers local
        $handlers = $this->getHandlers();

        do { // descent the directory structure down to find the (almost virtual) servlet file

            // bingo we found a (again: almost virtual) servlet file
            if (array_key_exists(".$extension", $handlers) && $handlers[".$extension"]->getName() === $this->getModuleName()) {

                // prepare the servlet path
                if ($dirname === '/') {
                    $servletPath = '/' . $basename;
                } else {
                    $servletPath = $dirname . '/' . $basename;
                }

                // we set the basename, because this is the servlet path
                $servletRequest->setServletPath($servletPath);

                // we set the path info, what is the request URI with stripped dir- and basename
                $servletRequest->setPathInfo(str_replace($servletPath, '', $uriWithoutQueryString));

                // we've found what we were looking for, so break here
                break;
            }

            // descendent down the directory tree
            list ($dirname, $basename, $extension) = array_values(pathinfo($dirname));

        } while ($dirname !== false); // stop until we reached the root of the URI
    }

    /**
     * Returns the server context instance.
     *
     * @return \TechDivision\Server\ServerContext The actual server context instance
     */
    public function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * Returns the initialized applications.
     *
     * @return \AppserverIo\Storage\GenericStackable The initialized application instances
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Returns the initialized valves.
     *
     * @return \AppserverIo\Storage\GenericStackable The initialized valves
     */
    public function getValves()
    {
        return $this->valves;
    }

    /**
     * Returns the initialized web server handlers.
     *
     * @return \AppserverIo\Storage\GenericStackable The initialized web server handlers
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
