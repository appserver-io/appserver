<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\GenericObjectFactory
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
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Psr\Di\ProviderInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A thread which creates timer instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
 * @property \AppserverIo\Storage\GenericStackable             $instances   The container for the factory created instances
 * @property string                                            $className   The fully qualified class name to return the instance for
 */
class GenericObjectFactory extends AbstractDaemonThread implements ObjectFactoryInterface
{

    /**
     * Initializes and the timer factory.
     */
    public function __construct()
    {
        $this->dispatched = false;
        $this->mutex = \Mutex::create();
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
     * Injects the container for factory created instances.
     *
     * @param \AppserverIo\Storage\GenericStackable $instances The container for the factory created instances
     *
     * @return void
     */
    public function injectInstances(GenericStackable $instances)
    {
        $this->instances = $instances;
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
     * Create a new instance with the passed data.
     *
     * @param string $className The fully qualified class name to return the instance for
     *
     * @return object The instance itself
     *
     * @throws \Exception
     */
    public function newInstance($className, $sessionId = null, array $args = array())
    {

        // lock the method
        \Mutex::lock($this->mutex);

        // we're not dispatched
        $this->dispatched = false;

        // initialize the data
        $this->className = $className;

        // notify the thread
        $this->notify();

        // wait till we've dispatched the request
        while ($this->dispatched === false) {
            usleep(100);
        }

        // try to load the last created instance
        if (isset($this->instances[$last = sizeof($this->instances) - 1])) {
            $instance = $this->instances[$last];
        } else {
            throw new \Exception('Requested instance can\'t be created');
        }

        // unlock the method
        \Mutex::unlock($this->mutex);

        // return the created instance
        return $instance;
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

        // synchronize the application instance and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($this->profileLogger = $this->getApplication()->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $this->profileLogger->appendThreadContext('generic-object-factory');
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

        // create the instance and stack it
        $this->synchronized(function ($self) {

            // create the instance only if we're NOT dispatched and a class name is available
            if ($self->dispatched === false && $self->className) {
                // create the instance
                $instance = $self->getApplication()->search(ProviderInterface::IDENTIFIER)
                                                   ->newInstance($self->className);

                // stack the instance
                $self->instances[] = $instance;

                // we're dispatched now
                $self->dispatched = true;
            }

        }, $this);
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

    /**
     * Let the daemon sleep for the passed value of miroseconds.
     *
     * @param integer $timeout The number of microseconds to sleep
     *
     * @return void
     */
    public function sleep($timeout)
    {
        $this->synchronized(function ($self) use ($timeout) {
            $self->wait($timeout);
        }, $this);
    }
}
