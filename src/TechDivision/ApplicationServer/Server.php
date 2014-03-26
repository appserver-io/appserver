<?php
/**
 * TechDivision\ApplicationServer\Server
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Extractors\PharExtractor;
use TechDivision\ApplicationServer\Interfaces\ProvisionerInterface;
use TechDivision\ApplicationServer\Interfaces\ExtractorInterface;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\ApplicationServer\Api\Node\AppserverNode;
use TechDivision\ApplicationServer\Utilities\StateKeys;
use \Psr\Log\LoggerInterface;

/**
 * This is the main server class that starts the application server
 * and creates a separate thread for each container found in the
 * configuration file.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class Server
{

    /**
     * Initialize the array for the running threads.
     *
     * @var array
     */
    protected $threads = array();

    /**
     * The system configuration.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\NodeInterface
     */
    protected $systemConfiguration;

    /**
     * The servers initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The servers webapp extractor
     *
     * @var \TechDivision\ApplicationServer\Interfaces\ExtractorInterface
     */
    protected $extractor;

    /**
     * The servers provisioners.
     *
     * @var array
     */
    protected $provisioners = array();

    /**
     * Initializes the the server with the parsed configuration file.
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration The parsed configuration file
     *
     * @return void
     */
    public function __construct(Configuration $configuration)
    {
        
        // initialize the configuration and the base directory
        $systemConfiguration = new AppserverNode();
        $systemConfiguration->initFromConfiguration($configuration);
        $this->setSystemConfiguration($systemConfiguration);
        
        // initialize the server instance
        $this->init();
    }

    /**
     * Initialize's the server instance.
     *
     * @return void
     */
    protected function init()
    {
        // init initial context
        $this->initInitialContext();
        // init the file system
        $this->initFileSystem();
        // init main system logger
        $this->initSystemLogger();
    }

    /**
     * Initialize the initial context instance.
     *
     * @return void
     */
    protected function initInitialContext()
    {
        $initialContextNode = $this->getSystemConfiguration()->getInitialContext();
        $reflectionClass = new \ReflectionClass($initialContextNode->getType());
        $initialContext = $reflectionClass->newInstanceArgs(array(
            $this->getSystemConfiguration()
        ));
        // set the initial context and flush it initially
        $this->setInitialContext($initialContext);
    }

    /**
     * Prepares filesystem to be sure that everything is on place as expected
     *
     * @return void
     */
    public function initFileSystem()
    {
        
        // init API service to use
        $service = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        
        // check if the log directory already exists, if not, create it
        foreach ($service->getDirectories() as $directory) {
            
            // prepare the path to the directory to be created
            $toBeCreated = $service->realpath($directory);
            // prepare the directory name and check if the directory already exists
            if (is_dir($toBeCreated) === false) {
                // if not create it
                mkdir($toBeCreated, 0755, true);
            }
        }
    }

    /**
     * Initialize the system logger.
     *
     * @return void
     */
    protected function initSystemLogger()
    {
        
        // initialize the logger instance itself
        $systemLoggerNode = $this->getSystemConfiguration()->getSystemLogger();
        $systemLogger = $this->newInstance($systemLoggerNode->getType(), array(
            $systemLoggerNode->getChannelName()
        ));
        
        // initialize the processors
        foreach ($systemLoggerNode->getProcessors() as $processorNode) {
            $processor = $this->newInstance($processorNode->getType(), $processorNode->getParamsAsArray());
            $systemLogger->pushProcessor($processor);
        }
        
        // initialize the handlers
        foreach ($systemLoggerNode->getHandlers() as $handlerNode) {
            $handler = $this->newInstance($handlerNode->getType(), $handlerNode->getParamsAsArray());
            $formatterNode = $handlerNode->getFormatter();
            $handler->setFormatter($this->newInstance($formatterNode->getType(), $formatterNode->getParamsAsArray()));
            $systemLogger->pushHandler($handler);
        }
        
        // set the initialized logger finally
        $this->getInitialContext()->setSystemLogger($systemLogger);
    }

    /**
     * Initializes the extractor.
     *
     * @return void
     */
    protected function initExtractor()
    {
        // @TODO: Read extractor type from configuration
        $this->setExtractor(new PharExtractor($this->getInitialContext()));
        // extract all webapps
        $this->getExtractor()->deployWebapps();
    }
    
    /**
     * Initializes the provisioners.
     * 
     * @return void
     */
    protected function initProvisioners()
    {
        // @TODO: Read provisioner type from configuration
        $this->addProvisioner(new DatasourceProvisioner($this->getInitialContext()));
        $this->addProvisioner(new StandardProvisioner($this->getInitialContext()));

        // invoke the provisioners
        foreach ($this->getProvisioners() as $provisioner) {
            $provisioner->provision();
        }
    }

    /**
     * Initialize the container threads.
     *
     * @return void
     */
    protected function initContainers()
    {
        
        // initialize the array for the threads
        $this->threads = array();
        
        // and initialize a container thread for each container
        foreach ($this->getSystemConfiguration()->getContainers() as $containerNode) {
            
            // initialize the container configuration with the base directory and pass it to the thread
            $params = array($this->getInitialContext(), $containerNode);
            
            // create and append the thread instance to the internal array
            $this->threads[] = $this->newInstance($containerNode->getThreadType(), $params);
        }
    }

    /**
     * Returns the running container threads.
     *
     * @return array Array with the running container threads
     */
    public function getThreads()
    {
        return $this->threads;
    }

    /**
     * Set's the system configuration.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $systemConfiguration The system configuration object
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The system configuration
     */
    public function setSystemConfiguration(NodeInterface $systemConfiguration)
    {
        return $this->systemConfiguration = $systemConfiguration;
    }

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->systemConfiguration;
    }

    /**
     * Set's the initial context instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     *
     * @return void
     */
    public function setInitialContext(InitialContext $initialContext)
    {
        return $this->initialContext = $initialContext;
    }

    /**
     * Returns the initial context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getSystemLogger()
    {
        return $this->getInitialContext()->getSystemLogger();
    }

    /**
     * Sets the extractor
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ExtractorInterface $extractor The initial context instance
     *            
     * @return void
     */
    public function setExtractor(ExtractorInterface $extractor)
    {
        return $this->extractor = $extractor;
    }

    /**
     * Returns the extractor
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ExtractorInterface The extractor instance
     */
    public function getExtractor()
    {
        return $this->extractor;
    }

    /**
     * Sets the provisioner.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ProvisionerInterface $provisioner The initial context instance
     *            
     * @return void
     */
    public function addProvisioner(ProvisionerInterface $provisioner)
    {
        return $this->provisioners[] = $provisioner;
    }

    /**
     * Returns the provisioners.
     *
     * @return array The provisioners
     */
    public function getProvisioners()
    {
        return $this->provisioners;
    }

    /**
     * Start the container threads.
     *
     * @return void
     * @see \TechDivision\ApplicationServer\Server::watch();
     */
    public function start()
    {
        
        // init the extractor
        $this->initExtractor();
        
        // init the provisioner
        $this->initProvisioners();
        
        // init the containers
        $this->initContainers();
        
        // log that the server will be started now
        $this->getSystemLogger()->info(
            sprintf(
                'Server successfully started in basedirectory %s ',
                $this->getSystemConfiguration()
                    ->getBaseDirectory()
                    ->getNodeValue()
                    ->__toString()
            )
        );
        
        // start the container threads
        $this->startContainers();
    }
    
    /**
     * Scan's the deployment directory for changes and restarts
     * the server instance if necessary.
     * 
     * This is an alternative method to call start() because the
     * monitor is running exclusively like the start() method.
     * 
     * @return void
     * @see \TechDivision\ApplicationServer\Server::start();
     */
    public function watch()
    {

        // initialize the default monitor for the deployment directory
        $monitor = $this->newInstance(
            'TechDivision\ApplicationServer\Scanner\DeploymentScanner',
            array($this->getInitialContext())
        );
        
        // start the monitor
        $monitor->start();
    }
    
    /**
     * Starts the registered container threads.
     * 
     * @return void
     */
    public function startContainers()
    {
        // set the flag that the application will be started
        $this->getInitialContext()->setAttribute(StateKeys::KEY, StateKeys::get(StateKeys::STARTING));
        
        // start the container threads
        foreach ($this->getThreads() as $thread) {
        
            // start the thread
            $thread->start();
        
            // synchronize container threads to avoid registring apps several times
            $thread->synchronized(function ($self) {
                $self->wait();
            }, $thread);
        }
        
        // set the flag that the application has been started
        $this->getInitialContext()->setAttribute(StateKeys::KEY, StateKeys::get(StateKeys::RUNNING));
    }
    
    /**
     * Stops the appserver by setting the apropriate flag in the 
     * initial context.
     * 
     * @return void
     */
    public function stopContainers()
    {

        // calculate the start time
        $start = microtime(true);
        
        // set the flag that the application has to be stopped
        $this->getInitialContext()->setAttribute(StateKeys::KEY, StateKeys::get(StateKeys::STOPPING));
                        
        // log a message with the time needed for restart
        $this->getSystemLogger()->info(
            sprintf(
                "Successfully stopped appserver (in %d sec)",
                microtime(true) - $start
            )
        );
    }

    /**
     * Redeploys the apps and restarts the appserver.
     *
     * @return void
     */
    public function restartContainers()
    {

        // log a message that the appserver will be restarted now
        $this->getSystemLogger()->info('Now restarting appserver');

        // calculate the start time
        $start = microtime(true);
                        
        // stop the container threads
        $this->stopContainers();
        
        // check if apps has to be redeployed
        $this->getExtractor()->deployWebapps();
        
        // reinitialize the container threads
        $this->initContainers();
        
        // start the container threads
        $this->startContainers();
                        
        // log a message with the time needed for restart
        $this->getSystemLogger()->info(
            sprintf(
                "Successfully restarted appserver (in %d sec)",
                microtime(true) - $start
            )
        );
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * Returns a new instance of the passed API service.
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }
}
