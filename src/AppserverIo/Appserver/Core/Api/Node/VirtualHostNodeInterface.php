<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\VirtualHostNodeInterface
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
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Interface for virtual host node implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface VirtualHostNodeInterface
{

    /**
     * Returns the virtual hosts name
     *
     * @return string The server's type
     */
    public function getName();

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
     * Returns the file headers nodes.
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Returns the headers as an associative array.
     *
     * @return array The array with the headers
     */
    public function getHeadersAsArray();

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
     * Getter for the injector, will lazy-init it
     *
     * @return \AppserverIo\Server\Configuration\Extension\InjectorInterface
     */
    public function getInjector();

    /**
     * Will return true if the class has a filled extensionType attribute and therefor an injector
     *
     * @return boolean
     */
    public function hasInjector();

    /**
     * Will return the data the defined injector creates
     *
     * @return mixed
     */
    public function getInjection();

    /**
     * Getter for extensionType attribute
     *
     * @return string
     */
    public function getExtensionType();

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
