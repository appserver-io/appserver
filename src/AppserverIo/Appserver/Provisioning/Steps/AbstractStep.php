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

use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\Api\Node\StepNode;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;
use AppserverIo\Appserver\Provisioning\Utilities\ParamKeys;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\ApplicationServer\ServiceInterface;
use AppserverIo\Psr\ApplicationServer\ContextInterface;
use AppserverIo\Psr\ApplicationServer\Configuration\DatasourceConfigurationInterface;

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
     * The maximum number of retries.
     *
     * @var integer
     */
    const MAX_RETRIES = 0;

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
     * @var \AppserverIo\Psr\ApplicationServer\Configuration\DatasourceConfigurationInterface
     */
    protected $datasourceNode;

    /**
     * The initial context.
     *
     * @var \AppserverIo\Psr\ApplicationServer\ContextInterface
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
     * @param \AppserverIo\Psr\ApplicationServer\ServiceInterface $service The provisioning service
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
     * @param \AppserverIo\Psr\ApplicationServer\Configuration\DatasourceConfigurationInterface $datasourceNode The datasource node data
     *
     * @return void
     */
    public function injectDatasourceNode(DatasourceConfigurationInterface $datasourceNode)
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
    public function injectInitialContext(ContextInterface $initialContext)
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
     * @return \AppserverIo\Psr\ApplicationServer\ServiceInterface The provisioning service
     */
    protected function getService()
    {
        return $this->service;
    }

    /**
     * Returns the step node data.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\StepNode The step node data
     */
    protected function getStepNode()
    {
        return $this->stepNode;
    }

    /**
     * Returns the datasource node found in the provisioning configuration.
     *
     * @return \AppserverIo\Psr\ApplicationServer\Configuration\DatasourceConfigurationInterface The datasource node data
     */
    protected function getDatasourceNode()
    {
        return $this->datasourceNode;
    }

    /**
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Psr\ApplicationServer\ContextInterface The initial context
     */
    protected function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the absolute path to the appservers PHP executable.
     *
     * @return string The absolute path to the appservers PHP executable
     */
    protected function getPhpExecutable()
    {
        return $this->phpExecutable;
    }

    /**
     * Returns the absolute path to the applications folder.
     *
     * @return string The applications folder
     */
    protected function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Will return the name of the application
     *
     * @return string
     */
    protected function getAppName()
    {
        return $this->getApplication()->getName();
    }

    /**
     * Will return the name of the application environment
     *
     * @return string
     */
    protected function getAppEnvironment()
    {
        return $this->getApplication()->getEnvironmentName();
    }

    /**
     * Return's the container instance the application is bound to.
     *
     * @return \AppserverIo\Psr\ApplicationServer\ContainerInterface The container instance
     */
    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    /**
     * Return's the container configuration instance.
     *
     * @return \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface The container configuration
     */
    protected function getContainerNode()
    {
        return $this->getContainer()->getContainerNode();
    }

    /**
     * Return's the system properties.
     *
     * @return \AppserverIo\Properties\PropertiesInterface The system properties
     */
    protected function getSystemProperties()
    {
        return $this->getApplication()->getSystemProperties();
    }

    /**
     * Return's the maximum number of retries.
     *
     * @return integer The maximum number
     */
    protected function getMaxRetries()
    {

        // try to load the number of maximum retries from the step configuration
        $maxRetries = $this->getStepNode()->getParam(ParamKeys::MAX_RETRIES);

        // return the number of maximum retries
        return $maxRetries ? $maxRetries : AbstractStep::MAX_RETRIES;
    }

    /**
     * Logs the start of the provisioning.
     *
     * @return void
     */
    protected function logStart()
    {
        \info(
            sprintf(
                'Now start to execute provisioning step %s for application %s (env %s)',
                $this->getStepNode()->getType(),
                $this->getAppName(),
                $this->getAppEnvironment()
            )
        );
    }

    /**
     * Logs the retry of the provisioning.
     *
     * @param integer $retry         The retry number
     * @param string  $failureReason The reason the last try failed
     *
     * @return void
     */
    protected function logRetry($retry, $failureReason)
    {
        \info(
            sprintf(
                'Provisioning step %s of application %s (env %s) failed %d (of %d) times with message "%s"',
                $this->getStepNode()->getType(),
                $this->getAppName(),
                $this->getAppEnvironment(),
                $retry,
                AbstractStep::MAX_RETRIES,
                $failureReason
            )
        );
    }

    /**
     * Logs the success of the provisioning.
     *
     * @return void
     */
    protected function logSuccess()
    {
        \info(
            sprintf(
                'Successfully executed provisioning step %s of application %s (env %s)',
                $this->getStepNode()->getType(),
                $this->getAppName(),
                $this->getAppEnvironment()
            )
        );
    }

    /**
     * Return's the param with the passed name.
     *
     * @param string $name The name of the param to return
     *
     * @return mixed The param value
     */
    protected function getParam($name)
    {
        return $this->getStepNode()->getParam($name);
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

        // create s simulated request/session ID whereas session equals request ID
        Environment::singleton()->setAttribute(EnvironmentKeys::SESSION_ID, $sessionId = SessionUtils::generateRandomString());
        Environment::singleton()->setAttribute(EnvironmentKeys::REQUEST_ID, $sessionId);

        // initialize retry flag and counter
        $retry = true;
        $retryCount = 0;

        // log a message that provisioning starts
        $this->logStart();

        do {
            try {
                // run the actual provisioning
                $this->execute();

                // log a message that provisioning has been successfull
                $this->logSuccess();

                // don't retry, because step has been successful
                $retry = false;
            } catch (\Exception $e) {
                // raise the retry count
                $retryCount++;
                // query whether or not we've reached the maximum retry count
                if ($retryCount < $this->getMaxRetries()) {
                    // sleep for an increasing number of seconds
                    sleep($retryCount + 1);
                    // debug log the exception
                    $this->logRetry($retryCount, $e->getMessage());
                } else {
                    // log a message and stop retrying
                    \error($e);
                    $retry = false;
                }
            }
        } while ($retry);
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
