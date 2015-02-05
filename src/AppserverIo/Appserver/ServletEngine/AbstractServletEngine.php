<?php

/**
 * AppserverIo\Appserver\ServletEngine\AbstractServletEngine
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\WebServer\Interfaces\HttpModuleInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationValve;

/**
 * Abstract servlet engine which provides basic functionality for child implementations
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Storage\GenericStackable $applications Storage with the available applications
 * @property \AppserverIo\Storage\GenericStackable $dependencies Storage with the available applications
 * @property \AppserverIo\Storage\GenericStackable $handlers     Storage handlers registered in the web server
 * @property \AppserverIo\Storage\GenericStackable $valves       Storage for the servlet engines valves that handles the request
 */
abstract class AbstractServletEngine extends GenericStackable implements HttpModuleInterface
{

    /**
     * Initialize the module
     */
    public function __construct()
    {

        /**
         * Storage with the available applications.
         *
         * @var \AppserverIo\Storage\GenericStackable
         */
        $this->dependencies = new GenericStackable();

        /**
         * Storage for the servlet engines valves that handles the request.
         *
         * @var \AppserverIo\Storage\GenericStackable
         */
        $this->valves = new GenericStackable();

        /**
         * Storage handlers registered in the web server.
         *
         * @var \AppserverIo\Storage\GenericStackable
         */
        $this->handlers = new GenericStackable();

        /**
         * Storage with the available applications.
         *
         * @var \AppserverIo\Storage\GenericStackable
         */
        $this->applications = new GenericStackable();
    }

    /**
     * Will try to find the application based on the context path taken from the requested filename.
     * Will return the found application on success and throw an exception if nothing could be found
     *
     * @param \AppserverIo\Server\Interfaces\RequestContextInterface $requestContext Context of the current request
     *
     * @return null|\AppserverIo\Psr\Application\ApplicationInterface
     *
     * @throws \AppserverIo\Appserver\ServletEngine\BadRequestException
     */
    protected function findRequestedApplication(RequestContextInterface $requestContext)
    {

        // prepare the request URL we want to match
        $webappsDir = $this->getServerContext()->getServerConfig()->getDocumentRoot();
        $relativeRequestPath = strstr($requestContext->getServerVar(ServerVars::DOCUMENT_ROOT) . $requestContext->getServerVar(ServerVars::X_REQUEST_URI), $webappsDir);
        $proposedAppName = strstr(str_replace($webappsDir . '/', '', $relativeRequestPath), '/', true);

        // try to match a registered application with the passed request
        foreach ($this->applications as $application) {
            if ($application->getName() === $proposedAppName) {
                return $application;
            }
        }

        // if we did not find anything we should throw a bad request exception
        throw new BadRequestException(sprintf('Can\'t find application for URL %s%s', $requestContext->getServerVar(ServerVars::HTTP_HOST), $requestContext->getServerVar(ServerVars::X_REQUEST_URI)));
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
     * Returns an array of module names which should be executed first.
     *
     * @return \AppserverIo\Storage\GenericStackable The module names this module depends on
     */
    public function getDependencies()
    {
        return $this->dependencies;
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

    /**
     * Returns the server context instance.
     *
     * @return \AppserverIo\Server\Interfaces\ServerContextInterface The actual server context instance
     */
    public function getServerContext()
    {
        return $this->serverContext;
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
     * Initialize the applications.
     *
     * @return void
     */
    public function initApplications()
    {
        $this->applications = $this->getServerContext()->getContainer()->getApplications();
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
     * Prepares the module for upcoming request in specific context
     *
     * @return boolean
     *
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function prepare()
    {
    }

    /**
     * Tries to find an application that matches the passed request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The request instance to locate the application for
     *
     * @return array The application info that matches the request
     *
     * @throws \AppserverIo\Appserver\ServletEngine\BadRequestException Is thrown if no application matches the request
     */
    protected function prepareServletRequest(HttpServletRequestInterface $servletRequest)
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

        // descent the directory structure down to find the (almost virtual) servlet file
        do {
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

            // descend down the directory tree
            list ($dirname, $basename, $extension) = array_values(pathinfo($dirname));

        } while ($dirname !== false); // stop until we reached the root of the URI
    }

    /**
     * Helper method that writes critical system exceptions to the system
     * logger if configured.
     *
     * @param \Exception $e The exception to be logged
     *
     * @return void
     */
    protected function logCriticalException(\Exception $e)
    {
        if ($this->getServerContext()->hasLogger(LoggerUtils::SYSTEM)) {
            $this->getServerContext()->getLogger(LoggerUtils::SYSTEM)->critical($e->__toString());
        }
    }
}
