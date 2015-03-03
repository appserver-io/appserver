<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ServerNodeInterface
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for the server node information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ServerNodeInterface extends NodeInterface
{

    /**
     * Returns the servers type.
     *
     * @return string The servers type
     */
    public function getType();

    /**
     * Returns the server name.
     *
     * @return mixed
     */
    public function getName();

    /**
     * Returns the worker to use for server.
     *
     * @return string The worker type to use for server
     */
    public function getWorker();

    /**
     * Returns the socket to use.
     *
     * @return string The socket type
     */
    public function getSocket();

    /**
     * Returns the loggers name to use.
     *
     * @return string The loggers name
     */
    public function getLoggerName();

    /**
     * Returns the server context to use.
     *
     * @return string The server context type
     */
    public function getServerContext();

    /**
     * Returns the request context to use.
     *
     * @return string The request context type
     */
    public function getRequestContext();

    /**
     * Returns the virtual hosts.
     *
     * @return array
     */
    public function getVirtualHosts();

    /**
     * Returns the connection handler nodes.
     *
     * @return array
     */
    public function getConnectionHandlers();

    /**
     * Returns the file handler nodes.
     *
     * @return array
     */
    public function getFileHandlers();

    /**
     * Returns the module nodes.
     *
     * @return array
     */
    public function getModules();

    /**
     * Will return the environment variables array.
     *
     * @return array The array with the environment variables
     */
    public function getEnvironmentVariables();

    /**
     * Will return the environmentVariable node with the specified definition and if nothing could
     * be found we will return false.
     *
     * @param string $definition The definition of the environmentVariable in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EnvironmentVariableNode|boolean The requested environmentVariable node
     */
    public function getEnvironmentVariable($definition);

    /**
     * Returns the environmentVariables as an associative array.
     *
     * @return array The array with the sorted environmentVariables
     */
    public function getEnvironmentVariablesAsArray();

    /**
     * Array with the handler params to use.
     *
     * @return array
     */
    public function getParams();

    /**
     * Array with the handler params to use.
     *
     * @param array $params The handler params
     *
     * @return void
     */
    public function setParams(array $params);

    /**
     * Sets the param with the passed name, type and value.
     *
     * @param string $name  The param name
     * @param string $type  The param type
     * @param mixed  $value The param value
     *
     * @return void
     */
    public function setParam($name, $type, $value);

    /**
     * Returns the param with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the param to be returned
     *
     * @return mixed The requested param casted to the specified type
     */
    public function getParam($name);

    /**
     * Returns the params casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted params
     */
    public function getParamsAsArray();

    /**
     * Will return rewriteMaps definitions
     *
     * @return array
     */
    public function getRewriteMaps();

    /**
     * Returns the rewriteMaps as an associative array.
     *
     * @return array The array with the rewriteMaps
     */
    public function getRewriteMapsAsArray();

    /**
     * Will return the rewrites array.
     *
     * @return array
     */
    public function getRewrites();

    /**
     * Will return the rewrite node with the specified condition and if nothing could be found we will return false.
     *
     * @param string $condition The condition of the rewrite in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\RewriteNode|boolean The requested rewrite node
     */
    public function getRewrite($condition);

    /**
     * Returns the rewrites as an associative array.
     *
     * @return array The array with the sorted rewrites
     */
    public function getRewritesAsArray();

    /**
     * Will return access definitions
     *
     * @return array
     */
    public function getAccesses();

    /**
     * Returns the rewrites as an associative array.
     *
     * @return array The array with the sorted rewrites
     */
    public function getAccessesAsArray();

    /**
     * Will return the locations array.
     *
     * @return array
    */
    public function getLocations();

    /**
     * Will return the location node with the specified condition and if nothing could be found we will return false.
     *
     * @param string $condition The condition of the location in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\LocationNode|boolean The requested location node
     */
    public function getLocation($condition);

    /**
     * Returns the locations as an associative array.
     *
     * @return array The array with the sorted locations
     */
    public function getLocationsAsArray();

    /**
     * Will return the authentications array.
     *
     * @return array The array with the authentications
     */
    public function getAuthentications();

    /**
     * Will return the authentication node with the specified definition and if nothing could
     * be found we will return false.
     *
     * @param string $uri The URI of the authentication in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthenticationNode|boolean The requested authentication node
     */
    public function getAuthentication($uri);

    /**
     * Returns the authentications as an associative array.
     *
     * @return array The array with the sorted authentications
     */
    public function getAuthenticationsAsArray();

    /**
     * Will return the analytics array
     *
     * @return array The array with the analytic nodes
     */
    public function getAnalytics();

    /**
     * Will return the analytic node with the specified definition and if nothing could
     * be found we will return false
     *
     * @param string $uri The URI of the analytic in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AnalyticNode|boolean The requested analytics node
     */
    public function getAnalytic($uri);

    /**
     * Returns the analytics as an associative array
     *
     * @return array The array with the sorted analytics
     */
    public function getAnalyticsAsArray();
}
