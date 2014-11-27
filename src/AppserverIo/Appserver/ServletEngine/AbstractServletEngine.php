<?php

/**
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
 * @subpackage ServletEngine
 * @author     Bernhard Wick <bw@appserver.io>
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Appserver\Application\VirtualHost;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationValve;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\WebServer\Interfaces\HttpModuleInterface;

/**
 * AppserverIo\Appserver\ServletEngine\AbstractServletEngine
 *
 * Abstract servlet engine which provides basic functionality for child implementations
 *
 * @category   Server
 * @package    Appserver
 * @subpackage ServletEngine
 * @author     Bernhard Wick <bw@appserver.io>
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
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

        /**
         * Storage with the registered virtual hosts.
         *
         * @var \AppserverIo\Storage\GenericStackable
         */
        $this->virtualHosts = new GenericStackable();

        /**
         * Storage with URL => application mappings.
         *
         * @var \AppserverIo\Storage\GenericStackable
         */
        $this->urlMappings = new GenericStackable();
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
     * @return boolean
     *
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function prepare()
    {
    }
}
