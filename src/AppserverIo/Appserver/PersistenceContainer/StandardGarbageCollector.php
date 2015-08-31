<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\StandardGarbageCollector
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The garbage collector for the stateful session beans.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardGarbageCollector extends AbstractDaemonThread
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
            $this->profileLogger->appendThreadContext('persistence-container-garbage-collector');
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
        $beanManager = $this->getApplication()->search('BeanContextInterface');

        // load the map with the stateful session beans
        /** @var \AppserverIo\Storage\StorageInterface $statefulSessionBeans */
        $statefulSessionBeans = $beanManager->getStatefulSessionBeans();

        // initialize the timestamp with the actual time
        $actualTime = time();

        // iterate over the applications sessions with stateful session beans
        foreach ($statefulSessionBeans->getLifetime() as $identifier => $lifetime) {
            // check the lifetime of the stateful session beans
            if ($lifetime < $actualTime) {
                // if the stateful session bean has timed out, remove it
                $statefulSessionBeans->remove($identifier, array($beanManager, 'destroyBeanInstance'));
                // write a log message
                $this->getApplication()
                     ->getNamingDirectory()
                     ->search('php:global/log/System')
                     ->debug(sprintf('Successfully removed SFSB %s', $identifier));
                // reduce CPU load
                usleep(1000);
            }
        }

        // profile the size of the sessions
        if ($this->profileLogger) {
            $this->profileLogger->debug(
                sprintf('Processed standard garbage collector, handling %d SFSBs', sizeof($statefulSessionBeans))
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
