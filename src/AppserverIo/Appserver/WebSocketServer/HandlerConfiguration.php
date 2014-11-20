<?php

/**
 * AppserverIo\Appserver\WebSocketServer\HandlerConfiguration
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
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\WebSocketServer;

use TechDivision\WebSocketProtocol\HandlerConfig;

/**
 * Handler configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class HandlerConfiguration implements HandlerConfig
{

    /**
     * The handlers name from the handler.xml configuration file.
     *
     * @var string
     */
    protected $handlerName;

    /**
     * The handler context instance.
     *
     * @var \TechDivision\WebSocketProtocol\HandlerContext
     */
    protected $handlerContext;

    /**
     * The absolute path to the web application.
     *
     * @var string
     */
    protected $webappPath;

    /**
     * Array with the servlets init parameters found in the handler.xml configuration file.
     *
     * @var array
     */
    protected $initParameter = array();

    /**
     * Injects the handler context instance.
     *
     * @param \TechDivision\WebSocketProtocol\HandlerContext $handlerContext The handler context instance
     *
     * @return void
     */
    public function injectHandlerContext($handlerContext)
    {
        $this->handlerContext = $handlerContext;
    }

    /**
     * Injects the handlers name from the handler.xml configuration file.
     *
     * @param string $handlerName The handler name
     *
     * @return void
     */
    public function injectHandlerName($handlerName)
    {
        $this->handlerName = $handlerName;
    }

    /**
     * Injects the absolute path to the web application.
     *
     * @param string $webappPath The path to this web application
     *
     * @return void
     */
    public function injectWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Returns the handler context instance.
     *
     * @return \TechDivision\WebSocketProtocol\HandlerContext The handler context instance
     */
    public function getHandlerContext()
    {
        return $this->handlerContext;
    }

    /**
     * Returns the webapp base path.
     *
     * @return string The webapp base path
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Returns the handlers name from the handler.xml configuration file.
     *
     * @return string The handler name
     */
    public function getHandlerName()
    {
        return $this->handlerName;
    }

    /**
     * Registers the init parameter under the passed name.
     *
     * @param string $name  Name to register the init parameter with
     * @param string $value The value of the init parameter
     *
     * @return void
     */
    public function addInitParameter($name, $value)
    {
        $this->initParameter[$name] = $value;
    }

    /**
     * Returns the init parameter with the passed name.
     *
     * @param string $name Name of the init parameter to return
     *
     * @return string The configuration value
     */
    public function getInitParameter($name)
    {
        if (array_key_exists($name, $this->initParameter)) {
            return $this->initParameter[$name];
        }
    }
}
