<?php

/**
 * AppserverIo\Appserver\Provisioning\Steps\AbstractStep
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

namespace AppserverIo\Appserver\Provisioning\Steps;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\StepNode;
use AppserverIo\Appserver\Core\Api\Node\DatasourceNode;
use AppserverIo\Appserver\Core\Api\ServiceInterface;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;

/**
 * Abstract base class for a step implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractStep extends \Thread implements StepInterface
{

    /**
     * The provisioning service.
     *
     * @var \AppserverIo\Appserver\Core\Api\ServiceInterface;
     */
    protected $service;

    /**
     * The step node with the configuration data for this step.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\StepNode
     */
    protected $stepNode;

    /**
     * The datasource node found in the provisioning configuration.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatasourceNode
     */
    protected $datasourceNode;

    /**
     * The initial context.
     *
     * @var \AppserverIo\Appserver\Application\Interfaces\ContextInterface
     */
    protected $initialContext;

    /**
     * The absolute path to the appserver PHP executable.
     *
     * @var string
     */
    protected $phpExecutable;

    /**
     * The absolute path to the applications folder.
     *
     * @var string
     */
    protected $webappPath;

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Injects the provisioning service.
     *
     * @param \AppserverIo\Appserver\Core\Api\ServiceInterface $service The provisioning service
     *
     * @return void
     */
    public function injectService(ServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Injects the step node with the configuration data for this step.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\StepNode $stepNode The step node data
     *
     * @return void
     */
    public function injectStepNode(StepNode $stepNode)
    {
        $this->stepNode = $stepNode;
    }

    /**
     * Injects the datasource node found in the provisioning configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\DatasourceNode $datasourceNode The datasource node data
     *
     * @return void
     */
    public function injectDatasourceNode(DatasourceNode $datasourceNode)
    {
        $this->datasourceNode = $datasourceNode;
    }

    /**
     * Injects the absolute path to the appservers PHP executable.
     *
     * @param string $phpExecutable The absolute path to the appservers PHP executable
     *
     * @return void
     */
    public function injectPhpExecutable($phpExecutable)
    {
        $this->phpExecutable = $phpExecutable;
    }

    /**
     * Injects the absolute path to the applications folder.
     *
     * @param string $webappPath The absolute path to applications folder
     *
     * @return void
     */
    public function injectWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Injects the initial context.
     *
     * @param \AppserverIo\Appserver\Core\InitialContext $initialContext The initial context instance
     *
     * @return void
     */
    public function injectInitialContext(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Injects the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Returns the provisioning service.
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface The provisioning service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the step node data.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\StepNode The step node data
     */
    public function getStepNode()
    {
        return $this->stepNode;
    }

    /**
     * Returns the datasource node found in the provisioning configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatasourceNode The datasource node data
     */
    public function getDatasourceNode()
    {
        return $this->datasourceNode;
    }

    /**
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ContextInterface The initial context
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the absolute path to the appservers PHP executable.
     *
     * @return string The absolute path to the appservers PHP executable
     */
    public function getPhpExecutable()
    {
        return $this->phpExecutable;
    }

    /**
     * Returns the absolute path to the applications folder.
     *
     * @return string The applications folder
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Executes the steps functionality in a separate context.
     *
     * @return void
     */
    public function run()
    {

        // register the default autoloader
        require SERVER_AUTOLOADER;

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));

        // synchronize the application instance and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // add the application instance to the environment
        Environment::singleton()->setAttribute(EnvironmentKeys::APPLICATION, $application);

        // execute the step functionality
        $this->execute();
    }

    /**
     * Shutdown function to log unexpected errors.
     *
     * @return void
     * @see http://php.net/register_shutdown_function
     */
    public function shutdown()
    {
        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize error type and message
            $type = 0;
            $message = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                $this->getInitialContext()->getSystemLogger()->critical($message);
            }
        }
    }
}
