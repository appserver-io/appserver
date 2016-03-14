<?php

/**
 * \AppserverIo\Appserver\ServletEngine\StandardGarbageCollector
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

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A thread that loads the session managers session handlers
 * and invokes their collectGarbage() method.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \Psr\Log\LoggerInterface                                      $systemLogger    The system logger instance
 * @property \Psr\Log\LoggerInterface                                      $profileLogger   The profile logger instance
 * @property \AppserverIo\Psr\Application\ApplicationInterface             $application     The application instance
 * @property \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface $sessionSettings Settings for the session handling
 */
class StandardGarbageCollector extends AbstractDaemonThread implements GarbageCollectorInterface
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
     * Return's the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Injects the session settings.
     *
     * @param \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface $sessionSettings Settings for the session handling
     *
     * @return void
     */
    public function injectSessionSettings($sessionSettings)
    {
        $this->sessionSettings = $sessionSettings;
    }

    /**
     * Returns the session settings.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface The session settings
     */
    public function getSessionSettings()
    {
        return $this->sessionSettings;
    }

    /**
     * Initializes and starts the garbage collector.
     *
     * @return void
     */
    public function initialize()
    {
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

        // register the application's class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // try to load the system logger
        $this->systemLogger = $this->getLogger(LoggerUtils::SYSTEM);

        // try to load the profile logger
        if ($this->profileLogger = $this->getLogger(LoggerUtils::PROFILE)) {
            $this->profileLogger->appendThreadContext('servlet-engine-garbage-collector');
        }
    }

    /**
     * Returns the default timeout.
     *
     * @return integer The default timeout in microseconds
     */
    public function getDefaultTimeout()
    {
        return parent::getDefaultTimeout() * 2;
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

        // collect the session garbage
        $this->collectGarbage();

        // profile the size of the sessions
        if ($profileLogger = $this->getProfileLogger()) {
            $profileLogger->info('Successfull collect garbage for the servlet engine\'s session manager');
        }
    }

    /**
     * Collects the session garbage.
     *
     * @return integer The number of expired and removed sessions
     */
    public function collectGarbage()
    {

        // the probability that we want to collect the garbage (float <= 1.0)
        $garbageCollectionProbability = $this->getSessionSettings()->getGarbageCollectionProbability();

        // calculate if the want to collect the garbage now
        $decimals = strlen(strrchr($garbageCollectionProbability, '.')) - 1;
        $factor = ($decimals > - 1) ? $decimals * 10 : 1;

        // if we can to collect the garbage, start collecting now
        if (rand(0, 100 * $factor) <= ($garbageCollectionProbability * $factor)) {
            // we want to know what inactivity timeout we've to check the sessions for
            $inactivityTimeout = $this->getSessionSettings()->getInactivityTimeout();
            // debug log the inactivity timeout we collect the garbage for
            if ($systemLogger = $this->getSystemLogger()) {
                $systemLogger->debug(
                    sprintf(
                        'Now collect garbage for probability %f and inactivity timeout %d',
                        $garbageCollectionProbability,
                        $inactivityTimeout
                    )
                );
            }

            // iterate over all session and collect the session garbage
            if ($inactivityTimeout !== 0) {
                // load the session manager instance
                /** @var \AppserverIo\Appserver\ServletEngine\SessionManagerInterface $sessionManager */
                $sessionManager = $this->getApplication()->search(SessionManagerInterface::IDENTIFIER);

                // iterate over all session managers and remove the expired sessions
                foreach ($sessionManager->getSessionHandlers() as $sessionHandlerName => $sessionHandler) {
                    try {
                        if ($systemLogger && ($sessionRemovalCount = $sessionHandler->collectGarbage()) > 0) {
                            $systemLogger->debug(
                                sprintf(
                                    'Successfully removed %d session(s) by session handler \'%s\'',
                                    $sessionRemovalCount,
                                    $sessionHandlerName
                                )
                            );
                        }

                    } catch (\Exception $e) {
                        if ($systemLogger) {
                            $systemLogger->error($e->__toString());
                        }
                    }
                }
            }
        }
    }

    /**
     * Return's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface|null The system logger insatnce
     */
    protected function getSystemLogger()
    {
        return $this->systemLogger;
    }

    /**
     * Return's the profile logger instance.
     *
     * @return \Psr\Log\LoggerInterface|null The profile logger insatnce
     */
    protected function getProfileLogger()
    {
        return $this->profileLogger;
    }

    /**
     * Return's the logger with the requested name. First we look in the
     * application and then in the system itself.
     *
     * @param string $loggerName The name of the logger to return
     *
     * @return \Psr\Log\LoggerInterface|null The logger with the requested name
     */
    protected function getLogger($loggerName)
    {

        try {
            // first let's see if we've an application logger registered
            if ($logger = $this->getApplication()->getLogger($loggerName)) {
                return $logger;
            }

            // then try to load the global logger instance if available
            return $this->getApplication()->getNamingDirectory()->search(sprintf('php:global/log/%s', $loggerName));

        } catch (NamingException $ne) {
            // do nothing, we simply have no logger with the requested name
        }
    }
}
