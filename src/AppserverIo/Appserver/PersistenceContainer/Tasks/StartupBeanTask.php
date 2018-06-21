<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\Tasks\StartupBeanTask
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
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Tasks;

use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\SingletonSessionBeanDescriptorInterface;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\AbstractExecutorThread;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;

/**
 * Executor thread implementation that executes a managers postStartup() lifecycle callback
 * after the application has been connected in a protected environment.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface                                    $application The application instance
 * @property \AppserverIo\Psr\EnterpriseBeans\Description\SingletonSessionBeanDescriptorInterface $descriptor  The descriptor instance
 */
class StartupBeanTask extends AbstractExecutorThread
{

    /**
     * Initializes the thread with the manager instance the postStartup() lifecycle
     * callback has to be invoked.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface                                    $application The manager instance
     * @param \AppserverIo\Psr\EnterpriseBeans\Description\SingletonSessionBeanDescriptorInterface $descriptor  The descriptor instance
     */
    public function __construct(ApplicationInterface $application, SingletonSessionBeanDescriptorInterface $descriptor)
    {

        // initialize the application and the descriptor
        $this->application = $application;
        $this->descriptor = $descriptor;

        // start the timer task
        $this->start(PTHREADS_INHERIT_NONE|PTHREADS_INHERIT_CONSTANTS);
    }

    /**
     * This method will be invoked before the while() loop starts and can be used
     * to implement some bootstrap functionality.
     *
     * @return void
     */
    public function bootstrap()
    {

        // setup autoloader
        require SERVER_AUTOLOADER;

        // make the application available and register the class loaders
        $application = $this->application;
        $application->registerClassLoaders();

        // add the application instance to the environment
        Environment::singleton()->setAttribute(EnvironmentKeys::APPLICATION, $application);

        // create s simulated request/session ID whereas session equals request ID
        Environment::singleton()->setAttribute(EnvironmentKeys::SESSION_ID, $sessionId = SessionUtils::generateRandomString());
        Environment::singleton()->setAttribute(EnvironmentKeys::REQUEST_ID, $sessionId);

        // try to load the profile logger
        if (isset($this->loggers[$profileLoggerKey = \AppserverIo\Logger\LoggerUtils::PROFILE])) {
            $this->profileLogger = $this->loggers[$profileLoggerKey];
            $this->profileLogger->appendThreadContext('timer-service-executor');
        }
    }

    /**
     * This method is the threads main method that'll be invoked once and has to
     * provide the threads business logic.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractExecutorThread::execute()
     */
    public function execute()
    {

        // make the descriptor and the application instance locally available
        $descriptor = $this->descriptor;
        $application = $this->application;

        // if we found a singleton session bean with a startup callback
        if ($descriptor instanceof SingletonSessionBeanDescriptorInterface && $descriptor->isInitOnStartup()) {
            $application->search($descriptor->getName());
        }
    }
}
