<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\GarbageCollectors\StartupBeanTaskGarbageCollector
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

namespace AppserverIo\Appserver\PersistenceContainer\GarbageCollectors;

use Psr\Log\LogLevel;
use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\BeanContextInterface;

/**
 * The garbage collector for the startup bean tasks.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StartupBeanTaskGarbageCollector extends AbstractDaemonThread
{

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
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
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

        // enable garbage collection
        gc_enable();

        // synchronize the application instance and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($this->profileLogger = $this->getApplication()->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $this->profileLogger->appendThreadContext('startup-bean-task-garbage-collector');
        }
    }

    /**
     * This is invoked on every iteration of the daemons while() loop.
     *
     * @param integer $timeout The timeout before the daemon wakes up
     *
     * @return void
     */
    public function iterate($timeout)
    {

        // call parent method and sleep for the default timeout
        parent::iterate($timeout);

        // collect the SFSBs that timed out
        $this->collectGarbage();
    }

    /**
     * Collects the SFSBs that has been timed out
     *
     * @return void
     */
    public function collectGarbage()
    {

        // we need the bean manager that handles all the beans
        /** @var \AppserverIo\Psr\EnterpriseBeans\BeanContextInterface $beanManager */
        $beanManager = $this->getApplication()->search(BeanContextInterface::IDENTIFIER);

        // load the map with the startup bean tasks
        /** @var \AppserverIo\Storage\GenericStackable $statefulSessionBeans */
        $startupBeanTasks = $beanManager->getStartupBeanTasks();

        // write a log message with size of startup bean tasks to be garbage collected
        $this->log(LogLevel::DEBUG, sprintf('Found "%d" startup been tasks to be garbage collected', sizeof($startupBeanTasks)));

        // iterate over the applications sessions with stateful session beans
        /** @var \Thread $startupBeanTask */
        foreach ($startupBeanTasks as $identifier => $startupBeanTask) {
            // check the lifetime of the stateful session beans
            if ($startupBeanTask->isRunning()) {
                // write a log message
                $this->log(LogLevel::DEBUG, sprintf('Startup bean task "%s" is still running', $identifier));
            } else {
                // remove the startup been task if it has been finished
                unset($startupBeanTasks[$identifier]);
                // write a log message
                $this->log(LogLevel::DEBUG, sprintf('Successfully removed startup bean task "%s"', $identifier));
                // reduce CPU load
                usleep(1000);
            }
        }

        // profile the size of the sessions
        /** @var \Psr\Log\LoggerInterface $this->profileLogger */
        if ($this->profileLogger) {
            $this->profileLogger->debug(
                sprintf('Processed startup been task garbage collector, handling "%d" startup bean tasks', sizeof($startupBeanTasks))
            );
        }
    }

    /**
     * This is a very basic method to log some stuff by using the error_log() method of PHP.
     *
     * @param mixed  $level   The log level to use
     * @param string $message The message we want to log
     * @param array  $context The context we of the message
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $this->getApplication()->getInitialContext()->getSystemLogger()->log($level, $message, $context);
    }
}
