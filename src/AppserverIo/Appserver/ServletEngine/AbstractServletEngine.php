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
 */
abstract class AbstractServletEngine implements HttpModuleInterface
{

    /**
     * Storage with the available applications.
     *
     * @var array
     */
    protected $dependencies;

    /**
     * Storage for the servlet engines valves that handles the request.
     *
     * @var array
     */
    protected $valves = array();

    /**
     * Storage handlers registered in the web server.
     *
     * @var array
     */
    protected $handlers = array();

    /**
     * Storage with the available applications.
     *
     * @var array
     */
    protected $applications = array();

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
     * @return array The initialized application instances
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Returns an array of module names which should be executed first.
     *
     * @return array The module names this module depends on
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Returns the initialized web server handlers.
     *
     * @return array The initialized web server handlers
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
     * @return array The initialized valves
     */
    public function getValves()
    {
        return $this->valves;
    }

    /**
     * Returns the container instance.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ContainerInterface The container instance
     */
    public function getContainer()
    {
        return $this->getServerContext()->getContainer();
    }

    /**
     * Initialize the applications.
     *
     * @return void
     */
    public function initApplications()
    {
        $this->applications = $this->getContainer()->getApplications();
    }

    /**
     * Initialize the web server handlers.
     *
     * @return void
     */
    public function initHandlers()
    {
        $this->handlers = $this->getServerContext()->getServerConfig()->getHandlers();
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
     * Helper method that writes debug system exceptions to the system
     * logger if configured.
     *
     * @param \Exception $e The exception to be logged
     *
     * @return void
     */
    protected function logDebugException(\Exception $e)
    {
        if ($this->getServerContext()->hasLogger(LoggerUtils::SYSTEM)) {
            $this->getServerContext()->getLogger(LoggerUtils::SYSTEM)->debug($e->__toString());
        }
    }

    /**
     * Helper method that writes system exceptions to the system
     * logger if configured.
     *
     * @param \Exception $e The exception to be logged
     *
     * @return void
     */
    protected function logErrorException(\Exception $e)
    {
        if ($this->getServerContext()->hasLogger(LoggerUtils::SYSTEM)) {
            $this->getServerContext()->getLogger(LoggerUtils::SYSTEM)->error($e->__toString());
        }
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
