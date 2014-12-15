<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistry
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\PersistenceContainerProtocol\BeanContext;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateless;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Psr\EnterpriseBeans\Annotations\MessageDriven;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The timer service registry handles an applications timer services.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class TimerServiceRegistry extends ServiceRegistry implements TimerServiceContext
{

    /**
     * The timer service executor.
     * 
     * @var \AppserverIo\Appserver\PersistenceContainer\ServiceExecutor
     */
    protected $timerServiceExecutor;
    
    /**
     * Injects the service executor for the timer service registry.
     * 
     * @param \AppserverIo\Appserver\PersistenceContainer\ServiceExecutor $timerServiceExecutor The service executor
     * 
     * @return void
     */
    public function injectTimerServiceExecutor(ServiceExecutor $timerServiceExecutor)
    {
        $this->timerServiceExecutor = $timerServiceExecutor;
    }
    
    /**
     * Returns the service executor for the timer service registry.
     * 
     * @return \AppserverIo\Appserver\PersistenceContainer\ServiceExecutor The timer service executor instance
     */
    public function getTimerServiceExecutor()
    {
        return $this->timerServiceExecutor;
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

        // register the class loader again, because each thread has its own context
        $application->registerClassLoaders();

        // build up META-INF directory var
        $metaInfDir = $application->getWebappPath() . DIRECTORY_SEPARATOR .'META-INF';

        // check if we've found a valid directory
        if (is_dir($metaInfDir) === false) {
            return;
        }
        
        // load the timer service executor
        $timerServiceExecutor = $this->getTimerServiceExecutor();

        // check meta-inf classes or any other sub folder to pre init beans
        $recursiveIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($metaInfDir));
        $phpFiles = new \RegexIterator($recursiveIterator, '/^(.+)\.php$/i');

        // iterate all php files
        foreach ($phpFiles as $phpFile) {

            try {

                // cut off the META-INF directory and replace OS specific directory separators
                $relativePathToPhpFile = str_replace(DIRECTORY_SEPARATOR, '\\', str_replace($metaInfDir, '', $phpFile));

                // now cut off the first directory, that'll be '/classes' by default
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
                $timedObjectInvoker->start();

                // initialize the stackable for the timers
                $timers = new StackableStorage();

                // initialize the timer service
                $timerService = new TimerService();
                $timerService->injectTimers($timers);
                $timerService->injectTimedObjectInvoker($timedObjectInvoker);
                $timerService->injectTimerServiceExecutor($timerServiceExecutor);
                $timerService->start();

                // register the initialized timer service
                $this->register($timerService);

                // log a message that the timer service has been registered
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf(
                        'Successfully registered timer service for bean %s',
                        $reflectionClass->getName()
                    )
                );

            } catch (\Exception $e) { // if class can not be reflected continue with next class

                // log an error message
                $application->getInitialContext()->getSystemLogger()->error($e->__toString());

                // proceed with the nexet bean
                continue;
            }
        }
    }

    /**
     * Attaches the passed service, to the context.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\ServiceProvider $instance The service instance to attach
     *
     * @return void
     * @throws \AppserverIo\Appserver\PersistenceContainer\ServiceAlreadyRegisteredException Is thrown if the passed service has already been registered
     */
    public function register(ServiceProvider $instance)
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
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return TimerServiceContext::IDENTIFIER;
    }
}
