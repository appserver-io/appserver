<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistry
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

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateless;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Psr\EnterpriseBeans\Annotations\MessageDriven;
use AppserverIo\Psr\EnterpriseBeans\ServiceProviderInterface;
use AppserverIo\Psr\EnterpriseBeans\ServiceExecutorInterface;
use AppserverIo\Psr\EnterpriseBeans\TimerServiceContextInterface;
use AppserverIo\Psr\EnterpriseBeans\ServiceAlreadyRegisteredException;

/**
 * The timer service registry handles an applications timer services.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class TimerServiceRegistry extends ServiceRegistry implements TimerServiceContextInterface
{

    /**
     * The timer service executor.
     *
     * @var \AppserverIo\Psr\EnterpriseBeans\ServiceExecutorInterface
     */
    protected $timerServiceExecutor;

    /**
     * The timer factory.
     *
     * @var \AppserverIo\Appserver\PersistenceContainer\TimerFactoryInterface
     */
    protected $timerFactory;

    /**
     * The calendar timer factory.
     *
     * @var \AppserverIo\Appserver\PersistenceContainer\CalendarTimerFactoryInterface
     */
    protected $calendarTimerFactory;

    /**
     * Injects the service executor for the timer service registry.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\ServiceExecutorInterface $timerServiceExecutor The service executor
     *
     * @return void
     */
    public function injectTimerServiceExecutor(ServiceExecutorInterface $timerServiceExecutor)
    {
        $this->timerServiceExecutor = $timerServiceExecutor;
    }

    /**
     * Injects the timer factory for the timer service registry.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\TimerFactoryInterface $timerFactory The timer factory
     *
     * @return void
     */
    public function injectTimerFactory(TimerFactoryInterface $timerFactory)
    {
        $this->timerFactory = $timerFactory;
    }

    /**
     * Injects the calendar timer factory for the timer service registry.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\CalendarTimerFactoryInterface $calendarTimerFactory The calendar timer factory
     *
     * @return void
     */
    public function injectCalendarTimerFactory(CalendarTimerFactoryInterface $calendarTimerFactory)
    {
        $this->calendarTimerFactory = $calendarTimerFactory;
    }

    /**
     * Returns the service executor for the timer service registry.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\ServiceExecutorInterface The timer service executor instance
     */
    public function getTimerServiceExecutor()
    {
        return $this->timerServiceExecutor;
    }

    /**
     * Returns the timer factory for the timer service registry.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerFactoryInterface The timer factory instance
     */
    public function getTimerFactory()
    {
        return $this->timerFactory;
    }

    /**
     * Returns the calendar timer factory for the timer service registry.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerFactoryInterface The calendar timer factory instance
     */
    public function getCalendarTimerFactory()
    {
        return $this->calendarTimerFactory;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function initialize(ApplicationInterface $application)
    {

        // build up META-INF directory var
        $metaInfDir = $application->getWebappPath() . DIRECTORY_SEPARATOR .'META-INF';

        // check if we've found a valid directory
        if (is_dir($metaInfDir) === false) {
            return;
        }

        // load the timer service executor and timer factories
        $timerFactory = $this->getTimerFactory();
        $calendarTimerFactory = $this->getCalendarTimerFactory();
        $timerServiceExecutor = $this->getTimerServiceExecutor();

        // load the service to iterate over application folders
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $service */
        $service = $application->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
        $phpFiles = $service->globDir($metaInfDir . DIRECTORY_SEPARATOR . '*.php');

        // iterate all php files
        foreach ($phpFiles as $phpFile) {
            try {
                // cut off the META-INF directory and replace OS specific directory separators
                $relativePathToPhpFile = str_replace(DIRECTORY_SEPARATOR, '\\', str_replace($metaInfDir, '', $phpFile));

                // now cut off the first directory, that will be '/classes' by default
                $pregResult = preg_replace('%^(\\\\*)[^\\\\]+%', '', $relativePathToPhpFile);
                $className = substr($pregResult, 0, -4);

                // create the reflection class instance
                $reflectionClass = new \ReflectionClass($className);

                // initialize the timed object instance with the data from the reflection class
                $timedObject = TimedObject::fromPhpReflectionClass($reflectionClass);

                // check if we have a bean with a @Stateless, @Singleton or @MessageDriven annotation
                if ($timedObject->hasAnnotation(Stateless::ANNOTATION) === false &&
                    $timedObject->hasAnnotation(Singleton::ANNOTATION) === false &&
                    $timedObject->hasAnnotation(MessageDriven::ANNOTATION) === false
                ) {
                    continue; // if not, we don't care here!
                }

                // initialize the stackable for the timeout methods
                $timeoutMethods = new StackableStorage();

                // create the timed object invoker
                $timedObjectInvoker = new TimedObjectInvoker();
                $timedObjectInvoker->injectApplication($application);
                $timedObjectInvoker->injectTimedObject($timedObject);
                $timedObjectInvoker->injectTimeoutMethods($timeoutMethods);
                $timedObjectInvoker->start(PTHREADS_INHERIT_NONE|PTHREADS_INHERIT_CONSTANTS);

                // initialize the stackable for the timers
                $timers = new StackableStorage();

                // initialize the timer service
                $timerService = new TimerService();
                $timerService->injectTimers($timers);
                $timerService->injectTimerFactory($timerFactory);
                $timerService->injectTimedObjectInvoker($timedObjectInvoker);
                $timerService->injectCalendarTimerFactory($calendarTimerFactory);
                $timerService->injectTimerServiceExecutor($timerServiceExecutor);
                $timerService->start(PTHREADS_INHERIT_NONE|PTHREADS_INHERIT_CONSTANTS);

                // register the initialized timer service
                $this->register($timerService);

                // log a message that the timer service has been registered
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf(
                        'Successfully registered timer service for bean %s',
                        $reflectionClass->getName()
                    )
                );

            // if class can not be reflected continue with next class
            } catch (\Exception $e) {
                // log an error message
                $application->getInitialContext()->getSystemLogger()->error($e->__toString());

                // proceed with the next bean
                continue;
            }
        }
    }

    /**
     * Attaches the passed service, to the context.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\ServiceProviderInterface $instance The service instance to attach
     *
     * @return void
     * @throws \AppserverIo\Psr\EnterpriseBeans\ServiceAlreadyRegisteredException Is thrown if the passed service has already been registered
     */
    public function register(ServiceProviderInterface $instance)
    {

        // check if the service has already been registered
        if ($this->getServices()->has($pk = $instance->getPrimaryKey())) {
            throw new ServiceAlreadyRegisteredException(
                sprintf(
                    'It is not allowed to register service %s with primary key %s more than on times',
                    $instance->getServiceName(),
                    $pk
                )
            );
        }

        // register the service using the primary key
        $this->getServices()->set($pk, $instance);
    }

    /**
     * Initializes the manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return TimerServiceContextInterface::IDENTIFIER;
    }

    /**
     * Shutdown the session manager instance.
     *
     * @return void
     * \AppserverIo\Psr\Application\ManagerInterface::stop()
     */
    public function stop()
    {
        $this->getTimerServiceExecutor()->stop();
        $this->getCalendarTimerFactory()->stop();
        $this->getTimerFactory()->stop();
    }
}
