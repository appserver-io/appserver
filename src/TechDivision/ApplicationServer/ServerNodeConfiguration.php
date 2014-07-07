<?php
/**
 * \TechDivision\ApplicationServer\ServerNodeConfiguration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_WebContainer
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\Server\Interfaces\ServerConfigurationInterface;

/**
 * Class ServerNodeConfiguration
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class ServerNodeConfiguration implements ServerConfigurationInterface
{

    /**
     * Hold's node instance
     *
     * @var \TechDivision\ApplicationServer\Api\Node\NodeInterface
     */
    protected $node;

    /**
     * Hold's the handlers array
     *
     * @var array
     */
    protected $handlers;

    /**
     * Hold's the connection handler array
     *
     * @var array
     */
    protected $connectionHandlers;

    /**
     * Hold's the virtual hosts array
     *
     * @var array
     */
    protected $virtualHosts;

    /**
     * Hold's the authentications array
     *
     * @var array
     */
    protected $authentications;

    /**
     * Hold's the modules array
     *
     * @var array
     */
    protected $modules;

    /**
     * Hold's the rewrites array
     *
     * @var array
     */
    protected $rewrites;

    /**
     * Hold's the accesses array
     *
     * @var array
     */
    protected $accesses;

    /**
     * Holds the environmentVariables array
     *
     * @var array
     */
    protected $environmentVariables;

    /**
     * Hold's the locations array.
     *
     * @var array
     */
    protected $locations;

    /**
     * Constructs config
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     */
    public function __construct($node)
    {
        $this->node = $node;
    }

    /**
     * Returns the username we want to execute the processes with.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->node->getParam('user');
    }

    /**
     * Returns the groupname we want to execute the processes with.
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->node->getParam('group');
    }

    /**
     * Return's type
     *
     * @return string
     */
    public function getType()
    {
        return $this->node->getType();
    }

    /**
     * Return's logger name
     *
     * @return string
     */
    public function getLoggerName()
    {
        return $this->node->getLoggerName();
    }

    /**
     * Return's servers name
     *
     * @return string
     */
    public function getName()
    {
        return $this->node->getName();
    }

    /**
     * Return's transport
     *
     * @return string
     */
    public function getTransport()
    {
        return $this->node->getParam('transport');
    }

    /**
     * Return's address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->node->getParam('address');
    }

    /**
     * Return's port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->node->getParam('port');
    }

    /**
     * Return's software
     *
     * @return string
     */
    public function getSoftware()
    {
        return $this->node->getParam('software');
    }

    /**
     * Return's admin
     *
     * @return string
     */
    public function getAdmin()
    {
        return $this->node->getParam('admin');
    }

    /**
     * Return's keep-alive max connection
     *
     * @return int
     */
    public function getKeepAliveMax()
    {
        return (int)$this->node->getParam('keepAliveMax');
    }

    /**
     * Return's keep-alive timeout
     *
     * @return int
     */
    public function getKeepAliveTimeout()
    {
        return (int)$this->node->getParam('keepAliveTimeout');
    }

    /**
     * Return's template file path for errors page
     *
     * @return string
     */
    public function getErrorsPageTemplatePath()
    {
        return (string)$this->node->getParam('errorsPageTemplatePath');
    }

    /**
     * Return's worker number
     *
     * @return int
     */
    public function getWorkerNumber()
    {
        return $this->node->getParam('workerNumber');
    }

    /**
     * Return's worker's accept min count
     *
     * @return int
     */
    public function getWorkerAcceptMin()
    {
        return $this->node->getParam('workerAcceptMin');
    }

    /**
     * Return's worker's accept max count
     *
     * @return int
     */
    public function getWorkerAcceptMax()
    {
        return $this->node->getParam('workerAcceptMax');
    }

    /**
     * Return's context type
     *
     * @return string
     */
    public function getServerContextType()
    {
        return $this->node->getServerContext();
    }

    /**
     * Return's request type
     *
     * @return string
     */
    public function getRequestContextType()
    {
        return $this->node->getRequestContext();
    }

    /**
     * Return's socket type
     *
     * @return string
     */
    public function getSocketType()
    {
        return $this->node->getSocket();
    }

    /**
     * Return's worker type
     *
     * @return string
     */
    public function getWorkerType()
    {
        return $this->node->getWorker();
    }

    /**
     * Return's document root
     *
     * @return string
     */
    public function getDocumentRoot()
    {
        return $this->node->getParam('documentRoot');
    }

    /**
     * Return's directory index definition
     *
     * @return string
     */
    public function getDirectoryIndex()
    {
        return $this->node->getParam('directoryIndex');
    }

    /**
     * Return's connection handlers
     *
     * @return array
     */
    public function getConnectionHandlers()
    {
        if (!$this->connectionHandlers) {
            $this->connectionHandlers = $this->prepareConnectionHandlers($this->node);
        }

        return $this->connectionHandlers;
    }

    /**
     * Return's modules
     *
     * @return array
     */
    public function getModules()
    {
        if (!$this->modules) {
            $this->modules = $this->prepareModules($this->node);
        }

        return $this->modules;
    }

    /**
     * Return's handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        if (!$this->handlers) {
            $this->handlers = $this->prepareHandlers($this->node);
        }

        return $this->handlers;
    }

    /**
     * Return's virtual hosts
     *
     * @return array
     */
    public function getVirtualHosts()
    {
        if (!$this->virtualHosts) {
            $this->virtualHosts = $this->prepareVirtualHosts($this->node);
        }

        return $this->virtualHosts;
    }

    /**
     * Returns the authentication configuration.
     *
     * @return array The array with the authentication configuration
     */
    public function getAuthentications()
    {
        if (!$this->authentications) {
            $this->authentications = $this->prepareAuthentications($this->node);
        }
        return $this->authentications;
    }

    /**
     * Return's cert path
     *
     * @return string
     */
    public function getCertPath()
    {
        return $this->node->getParam('certPath');
    }

    /**
     * Return's passphrase
     *
     * @return string
     */
    public function getPassphrase()
    {
        return $this->node->getParam('passphrase');
    }

    /**
     * Returns the rewrite configuration.
     *
     * @return array
     */
    public function getRewrites()
    {
        // init rewrites
        if (!$this->rewrites) {
            $this->rewrites = $this->prepareRewrites($this->node);
        }
        // return the rewrites
        return $this->rewrites;
    }

    /**
     * Returns the access configuration.
     *
     * @return array
     */
    public function getAccesses()
    {
        // init accesses
        if (!$this->accesses) {
            $this->accesses = $this->prepareAccesses($this->node);
        }
        return $this->accesses;
    }

    /**
     * Prepares the modules array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareModules(NodeInterface $node)
    {
        $modules = array();
        if (is_array($node->getModules())) {
            foreach ($node->getModules() as $module) {
                $modules[$module->getUuid()] = $module->getType();
            }
        }
        return $modules;
    }

    /**
     * Prepares the connectionHandlers array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareConnectionHandlers(NodeInterface $node)
    {
        $connectionHandlers = array();
        if (is_array($node->getConnectionHandlers())) {
            foreach ($node->getConnectionHandlers() as $connectionHandler) {
                $connectionHandlers[$connectionHandler->getUuid()] = $connectionHandler->getType();
            }
        }
        return $connectionHandlers;
    }

    /**
     * Prepares the handlers array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareHandlers(NodeInterface $node)
    {
        $handlers = array();
        if (is_array($node->getFileHandlers())) {
            foreach ($node->getFileHandlers() as $fileHandler) {
                $handlers[$fileHandler->getExtension()] = array(
                    'name' => $fileHandler->getName(),
                    'params' => $fileHandler->getParamsAsArray()
                );
            }
        }
        return $handlers;
    }

    /**
     * Prepares the virtual hosts array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareVirtualHosts(NodeInterface $node)
    {
        $virtualHosts = array();

        if (is_array($node->getVirtualHosts())) {
            // iterate hosts
            foreach ($node->getVirtualHosts() as $virtualHost) {
                $virtualHostNames = explode(' ', $virtualHost->getName());

                // Some virtual hosts might have an extensionType to expand their name attribute, check for that
                if ($virtualHost->hasInjector()) {

                    $virtualHostNames = array_merge($virtualHostNames, explode(' ', $virtualHost->getInjection()));
                }

                foreach ($virtualHostNames as $virtualHostName) {
                    // set all virtual hosts params per key for faster matching later on
                    $virtualHosts[trim($virtualHostName)] = array(
                        'params' => $virtualHost->getParamsAsArray(),
                        'rewriteMaps' => $this->prepareRewriteMaps($virtualHost),
                        'rewrites' => $this->prepareRewrites($virtualHost),
                        'environmentVariables' => $this->prepareEnvironmentVariables($virtualHost),
                        'accesses' => $this->prepareAccesses($virtualHost),
                        'locations' => $this->prepareLocations($virtualHost),
                        'authentications' => $this->prepareAuthentications($virtualHost),
                    );
                }
            }
        }
        return $virtualHosts;
    }

    /**
     * Prepares the rewrites array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareRewrites(NodeInterface $node)
    {
        $rewrites = array();

        if (is_array($node->getRewrites())) {
            // prepare the array with the rewrite rules
            foreach ($node->getRewrites() as $rewrite) {

                // Rewrites might be extended using different injector extension types, check for that
                if ($rewrite->hasInjector()) {

                    $target = $rewrite->getInjection();

                } else {

                    $target = $rewrite->getTarget();
                }

                // Build up the array entry
                $rewrites[] = array(
                    'condition' => $rewrite->getCondition(),
                    'target' => $target,
                    'flag' => $rewrite->getFlag()
                );
            }
        }
        return $rewrites;
    }

    /**
     * Prepares the locations array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareLocations(NodeInterface $node)
    {
        $locations = array();
        if (is_array($node->getLocations())) {
            // prepare the array with the locations
            foreach ($node->getLocations() as $location) {
                // Build up the array entry
                $locations[] = array(
                    'condition' => $location->getCondition(),
                    'handlers' => $this->prepareHandlers($location)
                );
            }
        }
        return $locations;
    }

    /**
     * Prepares the environmentVariables array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareEnvironmentVariables(NodeInterface $node)
    {
        $environmentVariables = array();
        // Get the nodes from our main node
        $environmentVariables = $node->getEnvironmentVariablesAsArray();
        return $environmentVariables;
    }

    /**
     * Returns the environment variable configuration
     *
     * @return array
     */
    public function getEnvironmentVariables()
    {
        // init EnvironmentVariables
        if (!$this->environmentVariables) {
            // Get the nodes from our main node
            $this->environmentVariables = $this->prepareEnvironmentVariables($this->node);
        }
        // return the environmentVariables
        return $this->environmentVariables;
    }

    /**
     * Prepares the authentications array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareAuthentications(NodeInterface $node)
    {
        $authentications = array();
        if (is_array($node->getAuthentications())) {
            foreach ($node->getAuthentications() as $authentication) {
                $authenticationUri = $authentication->getUri();
                $authentications[$authenticationUri] = $authentication->getParamsAsArray();
            }
        }
        return $authentications;
    }

    /**
     * Prepares the access array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareAccesses(NodeInterface $node)
    {
        $accesses = array();
        if (is_array($node->getAccesses())) {
            foreach ($node->getAccesses() as $access) {
                $accessType = $access->getType();
                // set all accesses information's
                $accesses[$accessType][] = $access->getParamsAsArray();
            }
        }
        return $accesses;
    }

    /**
     * Prepares the rewrite maps array based on a node implementing NodeInterface
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $node The node instance
     *
     * @return array
     */
    public function prepareRewriteMaps(NodeInterface $node)
    {
        $rewriteMaps = array();
        if (is_array($node->getRewriteMaps())) {
            foreach ($node->getRewriteMaps() as $rewriteMap) {
                $rewriteMapType = $rewriteMap->getType();
                // set all rewrite maps information's
                $rewriteMaps[$rewriteMapType] = $rewriteMap->getParamsAsArray();
            }
        }
        return $rewriteMaps;
    }

    /**
     * Returns the locations.
     *
     * @return array
     */
    public function getLocations()
    {
        // init locations
        if (!$this->locations) {
            $this->locations = $this->prepareLocations($this->node);
        }
        // return the locations
        return $this->locations;
    }

    /**
     * Returns the locations.
     *
     * @return array
     */
    public function getRewriteMaps()
    {
        // init locations
        if (!$this->rewriteMaps) {
            $this->rewriteMaps = $this->prepareRewriteMaps($this->node);
        }
        // return the locations
        return $this->rewriteMaps;
    }
}
