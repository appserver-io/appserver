<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AppserverNode
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

use AppserverIo\Description\Api\Node\NodeValue;
use AppserverIo\Description\Api\Node\AbstractNode;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\Utilities\FileKeys;
use AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface;

/**
 * DTO to transfer the application server's complete configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AppserverNode extends AbstractNode implements SystemConfigurationInterface
{

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SystemPropertiesNodeTrait
     */
    use SystemPropertiesNodeTrait;

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ConsolesNodeTrait
     */
    use ConsolesNodeTrait;

    /**
     * The node containing information about the initial context.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\InitialContextNode @AS\Mapping(nodeName="initialContext", nodeType="AppserverIo\Appserver\Core\Api\Node\InitialContextNode")
     */
    protected $initialContext;

    /**
     * Array with nodes for the registered loggers.
     *
     * @var array @AS\Mapping(nodeName="loggers/logger", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\LoggerNode")
     */
    protected $loggers = array();

    /**
     * Array with nodes for the registered extractors.
     *
     * @var array @AS\Mapping(nodeName="extractors/extractor", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ExtractorNode")
     */
    protected $extractors = array();

    /**
     * Array with nodes for the registered containers.
     *
     * @var array @AS\Mapping(nodeName="containers/container", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ContainerNode")
     */
    protected $containers = array();

    /**
     * Array with the information about the deployed applications.
     *
     * @var array @AS\Mapping(nodeName="apps/app", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\AppNode")
     */
    protected $apps = array();

    /**
     * Array with nodes for the registered datasources.
     *
     * @var array @AS\Mapping(nodeName="datasources/datasource", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\DatasourceNode")
     */
    protected $datasources = array();

    /**
     * Array with nodes for the registered scanners.
     *
     * @var array @AS\Mapping(nodeName="scanners/scanner", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ScannerNode")
     */
    protected $scanners = array();

    /**
     * Initializes the node with default values.
     */
    public function __construct()
    {
        // initialize the default configuration
        $this->initDefaultDirectories();
        $this->initDefaultFiles();
        $this->initDefaultExtractors();
        $this->initDefaultInitialContext();
    }

    /**
     * Initialize the default directories.
     *
     * @return void
     */
    protected function initDefaultDirectories()
    {
        $this->setParam(DirectoryKeys::TMP, ParamNode::TYPE_STRING, '/tmp');
        $this->setParam(DirectoryKeys::DEPLOY, ParamNode::TYPE_STRING, '/deploy');
        $this->setParam(DirectoryKeys::VENDOR, ParamNode::TYPE_STRING, '/vendor');
        $this->setParam(DirectoryKeys::WEBAPPS, ParamNode::TYPE_STRING, '/webapps');
        $this->setParam(DirectoryKeys::VAR_LOG, ParamNode::TYPE_STRING, '/var/log');
        $this->setParam(DirectoryKeys::VAR_RUN, ParamNode::TYPE_STRING, '/var/run');
        $this->setParam(DirectoryKeys::VAR_TMP, ParamNode::TYPE_STRING, '/var/tmp');
        $this->setParam(DirectoryKeys::ETC, ParamNode::TYPE_STRING, '/etc');
        $this->setParam(DirectoryKeys::ETC_APPSERVER, ParamNode::TYPE_STRING, '/etc/appserver');
        $this->setParam(DirectoryKeys::ETC_APPSERVER_CONFD, ParamNode::TYPE_STRING, '/etc/appserver/conf.d');
    }

    /**
     * Initialize the default files.
     *
     * @return void
     */
    protected function initDefaultFiles()
    {
        $logDir = $this->getParam(DirectoryKeys::VAR_LOG) . DIRECTORY_SEPARATOR;
        $this->setParam(FileKeys::APPSERVER_ERRORS_LOG, ParamNode::TYPE_STRING, $logDir . 'appserver-errors.log');
        $this->setParam(FileKeys::APPSERVER_ACCESS_LOG, ParamNode::TYPE_STRING, $logDir . 'appserver-access.log');
    }

    /**
     * Initializes the default initial context configuration.
     *
     * @return void
     */
    protected function initDefaultInitialContext()
    {

        // initialize the configuration values for the initial context
        $description = new DescriptionNode(new NodeValue('The initial context configuration.'));
        $storage = new StorageNode('AppserverIo\Storage\StackableStorage');

        // set the default initial context configuration
        $this->initialContext = new InitialContextNode('AppserverIo\Appserver\Core\InitialContext', $description, $storage);
    }

    /**
     * Initializes the default extractors for archive based deployment.
     *
     * @return void
     */
    protected function initDefaultExtractors()
    {

        // initialize the extractor
        $pharExtractor = new ExtractorNode(
            'phar',
            'AppserverIo\Appserver\Core\Extractors\PharExtractor',
            'AppserverIo\Appserver\Core\Extractors\PharExtractorFactory'
        );

        // add extractor to the appserver node
        $this->extractors[$pharExtractor->getPrimaryKey()] = $pharExtractor;
    }

    /**
     * Returns the username configured in the system configuration.
     *
     * @return string The username
     */
    public function getUser()
    {
        return $this->getParam('user');
    }

    /**
     * Returns the groupname configured in the system configuration.
     *
     * @return string The groupname
     */
    public function getGroup()
    {
        return $this->getParam('group');
    }

    /**
     * Returns the umask configured in the system configuration.
     *
     * @return string The umask
     */
    public function getUmask()
    {
        return $this->getParam('umask');
    }

    /**
     * Queries whether application configuration for container, server and virtual host
     * is allowed or not.
     *
     * @return boolean TRUE if applications can provide additional configuration, else FALSE
     */
    public function getAllowApplicationConfiguration()
    {
        return $this->getParam('allowApplicationConfiguration');
    }

    /**
     * Returns the node with the base directory information.
     *
     * @return string The base directory information
     */
    public function getBaseDirectory()
    {
        return $this->getParam(DirectoryKeys::BASE);
    }

    /**
     * Returns the node containing information about the initial context.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InitialContextNode The initial context information
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the array with all available loggers.
     *
     * @return array The available loggers
     */
    public function getLoggers()
    {
        return $this->loggers;
    }

    /**
     * Returns the array with registered extractors.
     *
     * @return array The registered extractors
     */
    public function getExtractors()
    {
        return $this->extractors;
    }

    /**
     * Returns the array with registered provisioners.
     *
     * @return array The registered provisioners
     */
    public function getProvisioners()
    {
        return $this->provisioners;
    }

    /**
     * Returns the array with all available containers.
     *
     * @return array The available containers
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Returns the container with the passed name.
     *
     * @param string $name The name of the container to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface The container node matching the passed name
     */
    public function getContainer($name)
    {
        // try to match one of the container names with the passed name
        /** @var \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $container */
        foreach ($this->getContainers() as $container) {
            if (fnmatch($name, $container->getName())) {
                return $container;
            }
        }
    }

    /**
     * Returns the containers as array with the container name as key.
     *
     * @return array The array with the containers
     */
    public function getContainersAsArray()
    {

        // initialize the array for the containers
        $containers = array();

        // iterate over all found containers and assemble the array
        /** @var \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $container */
        foreach ($this->getContainers() as $container) {
            $containers[$container->getName()] = $container;
        }

        // return the array with the containers
        return $containers;
    }

    /**
     * Attaches the passed container node.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $container The container node to attach
     *
     * @return void
     */
    public function attachContainer(ContainerNodeInterface $container)
    {
        $this->containers[$container->getPrimaryKey()] = $container;
    }

    /**
     * Returns an array with the information about the deployed applications.
     *
     * @return array The array with the information about the deployed applications
     */
    public function getApps()
    {
        return $this->apps;
    }

    /**
     * Attaches the passed app node.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\AppNode $app The app node to attach
     *
     * @return void
     */
    public function attachApp(AppNode $app)
    {
        $this->apps[$app->getPrimaryKey()] = $app;
    }

    /**
     * Returns an array with the information about the deployed datasources.
     *
     * @return array The array with the information about the deployed datasources
     */
    public function getDatasources()
    {
        return $this->datasources;
    }

    /**
     * Attaches the passed datasource node.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\DatasourceNode $datasource The datasource node to attach
     *
     * @return void
     */
    public function attachDatasource(DatasourceNode $datasource)
    {
        $this->datasources[$datasource->getPrimaryKey()] = $datasource;
    }

    /**
     * Returns the array with all available scanners.
     *
     * @return array The available scanners
     */
    public function getScanners()
    {
        return $this->scanners;
    }
}
