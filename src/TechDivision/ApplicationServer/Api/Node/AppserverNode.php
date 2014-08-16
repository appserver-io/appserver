<?php
/**
 * TechDivision\ApplicationServer\Api\Node\AppserverNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer the application server's complete configuration.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class AppserverNode extends AbstractNode
{

    /**
     * A params node trait.
     *
     * @var \TraitInterface
     */
    use ParamsNodeTrait;

    /**
     * The node containing information about the base directory.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode @AS\Mapping(nodeName="baseDirectory", nodeType="TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode")
     */
    protected $baseDirectory;

    /**
     * The node containing information about the initial context.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\InitialContextNode @AS\Mapping(nodeName="initialContext", nodeType="TechDivision\ApplicationServer\Api\Node\InitialContextNode")
     */
    protected $initialContext;
    /**
     * Array with nodes for the registered loggers.
     *
     * @var array @AS\Mapping(nodeName="loggers/logger", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\LoggerNode")
     */
    protected $loggers = array();

    /**
     * Array with nodes for the registered extractors.
     *
     * @var array @AS\Mapping(nodeName="extractors/extractor", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ExtractorNode")
     */
    protected $extractors = array();

    /**
     * Array with nodes for the registered provisioners.
     *
     * @var array @AS\Mapping(nodeName="provisioners/provisioner", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ProvisionerNode")
     */
    protected $provisioners = array();

    /**
     * Array with nodes for the registered containers.
     *
     * @var array @AS\Mapping(nodeName="containers/container", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ContainerNode")
     */
    protected $containers = array();

    /**
     * Array with the information about the deployed applications.
     *
     * @var array @AS\Mapping(nodeName="apps/app", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\AppNode")
     */
    protected $apps = array();

    /**
     * Array with nodes for the registered datasources.
     *
     * @var array @AS\Mapping(nodeName="datasources/datasource", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\DatasourceNode")
     */
    protected $datasources = array();

    /**
     * Initializes the node with default values.
     */
    public function __construct()
    {
        // initialize the default configuration
        $this->initDefaultLoggers();
        $this->initDefaultExtractors();
        $this->initDefaultProvisioners();
        $this->initDefaultInitialContext();
    }

    /**
     * Initializes the default initial context configuration.
     *
     * @return void
     */
    public function initDefaultInitialContext()
    {

        // initialize the configuration values for the initial context
        $description = new DescriptionNode(new NodeValue('The initial context configuration.'));
        $classLoader = new ClassLoaderNode('default', 'TechDivision\ApplicationServer\SplClassLoader');
        $storage = new StorageNode('TechDivision\Storage\StackableStorage');

        // set the default initial context configuration
        $this->initialContext = new InitialContextNode('TechDivision\ApplicationServer\InitialContext', $description, $classLoader, $storage);
    }

    /**
     * Initializes the default extractors for archive based deployment.
     *
     * @return void
     */
    protected function initDefaultExtractors()
    {

        // initialize the extractor
        $pharExtractor = new ExtractorNode('phar', 'TechDivision\ApplicationServer\Extractors\PharExtractor');

        // add extractor to the appserver node
        $this->extractors[$pharExtractor->getPrimaryKey()] = $pharExtractor;
    }

    /**
     * Initializes the default provisioners for database and
     * application database relation.
     *
     * @return void
     */
    protected function initDefaultProvisioners()
    {

        // initialize the provisioners
        $datasourceProvisioner = new ProvisionerNode('datasource', 'TechDivision\ApplicationServer\DatasourceProvisioner');
        $standardProvisioner = new ProvisionerNode('standard', 'TechDivision\ApplicationServer\StandardProvisioner');

        // add the provisioners to the appserver node
        $this->provisioners[$datasourceProvisioner->getPrimaryKey()] = $datasourceProvisioner;
        $this->provisioners[$standardProvisioner->getPrimaryKey()] = $standardProvisioner;
    }

    /**
     * Initializes the default logger configuration.
     *
     * @return void
     */
    protected function initDefaultLoggers()
    {

        // we need an introspection processor
        $processors = array();
        $processor = new ProcessorNode('\Monolog\Processor\IntrospectionProcessor');
        $processors[$processor->getPrimaryKey()] = $processor;

        // and a line formatter
        $formatter = new FormatterNode('\Monolog\Formatter\LineFormatter');

        // initialize the params for the system logger handler
        $handlerParams = array();
        $streamParam = new ParamNode('stream', 'string', new NodeValue('var/log/appserver-errors.log'));
        $levelParam = new ParamNode('level', 'integer', new NodeValue(200));
        $bubbleParam = new ParamNode('bubble', 'boolean', new NodeValue(false));
        $handlerParams[$streamParam->getPrimaryKey()] = $streamParam;
        $handlerParams[$levelParam->getPrimaryKey()] = $levelParam;
        $handlerParams[$bubbleParam->getPrimaryKey()] = $bubbleParam;

        // initialize the handler
        $handlers = array();
        $handler = new HandlerNode('\Monolog\Handler\StreamHandler', $formatter, $handlerParams);
        $handlers[$handler->getPrimaryKey()] = $handler;

        // initialize the system logger with the processor and the handlers
        $systemLogger = new LoggerNode('System', '\Monolog\Logger', 'system', $processors, $handlers);

        // initialize the params for the access logger formatter
        $formatterParams = array();
        $formatParam = new ParamNode('format', 'string', new NodeValue('%message%'));
        $dateFormatParam = new ParamNode('dateFormat', 'string', new NodeValue('Y-m-d H:i:s'));
        $allowInlineLineBreaksParam = new ParamNode('allowInlineLineBreaks', 'boolean', new NodeValue(true));
        $formatterParams[$formatParam->getPrimaryKey()] = $formatParam;
        $formatterParams[$dateFormatParam->getPrimaryKey()] = $dateFormatParam;
        $formatterParams[$allowInlineLineBreaksParam->getPrimaryKey()] = $allowInlineLineBreaksParam;

        // initialize the formatter for the access logger
        $formatter = new FormatterNode('\Monolog\Formatter\LineFormatter', $formatterParams);

        // initialize the params for the access logger handler
        $handlerParams = array();
        $streamParam = new ParamNode('stream', 'string', new NodeValue('var/log/appserver-access.log'));
        $levelParam = new ParamNode('level', 'integer', new NodeValue(100));
        $bubbleParam = new ParamNode('bubble', 'boolean', new NodeValue(true));
        $handlerParams[$streamParam->getPrimaryKey()] = $streamParam;
        $handlerParams[$levelParam->getPrimaryKey()] = $levelParam;
        $handlerParams[$bubbleParam->getPrimaryKey()] = $bubbleParam;

        // initialize the handler for the access logger
        $handlers = array();
        $handler = new HandlerNode('\Monolog\Handler\StreamHandler', $formatter, $handlerParams);
        $handlers[$handler->getPrimaryKey()] = $handler;

        // initialize the access logger
        $accessLogger = new LoggerNode('Access', '\Monolog\Logger', 'access', $processors, $handlers);

        // add the loggers to the default logger configuration
        $this->loggers[$systemLogger->getPrimaryKey()] = $systemLogger;
        $this->loggers[$accessLogger->getPrimaryKey()] = $accessLogger;
    }

    /**
     * Returns the username configured in the system configuration.
     *
     * @return string The username
     */
    public function getUser()
    {
        $this->getParam('user');
    }

    /**
     * Returns the groupname configured in the system configuration.
     *
     * @return string The groupname
     */
    public function getGroup()
    {
        $this->getParam('group');
    }

    /**
     * Returns the umask configured in the system configuration.
     *
     * @return string The umask
     */
    public function getUmask()
    {
        $this->getParam('umask');
    }

    /**
     * Sets the passed base directory node.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode $baseDirectory The base directory node to set
     *
     * @return void
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * Returns the node with the base directory information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode The base directory information
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Returns the node containing information about the initial context.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode The initial context information
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
     * @param \TechDivision\ApplicationServer\Api\Node\AppNode $app The app node to attach
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
     * @param \TechDivision\ApplicationServer\Api\Node\DatasourceNode $datasource The datasource node to attach
     *
     * @return void
     */
    public function attachDatasource(DatasourceNode $datasource)
    {
        $this->datasources[$datasource->getPrimaryKey()] = $datasource;
    }
}
