<?php
/**
 * \AppserverIo\Appserver\PersistenceContainer\TimedObjectInvoker
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

use Doctrine\Common\Annotations\AnnotationReader;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Lang\Reflection\MethodInterface;
use AppserverIo\Psr\Naming\InitialContext;
use AppserverIo\Psr\EnterpriseBeans\TimerInterface;
use AppserverIo\Psr\EnterpriseBeans\TimedObjectInterface;
use AppserverIo\Psr\EnterpriseBeans\TimedObjectInvokerInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Timeout;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Schedule;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;

/**
 * Timed object invoker for an enterprise bean.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface $application          The application instance
 * @property \AppserverIo\Lang\Reflection\ClassInterface       $timedObject          The timed object instance
 * @property \AppserverIo\Storage\StorageInterface             $timeoutMethods       The storage for the timeout methods
 * @property \AppserverIo\Lang\Reflection\MethodInterface      $defaultTimeoutMethod The timeout method instance
 */
class TimedObjectInvoker extends GenericStackable implements TimedObjectInvokerInterface
{

    /**
     * The execution environment.
     *
     * @var \AppserverIo\Appserver\Core\Environment
     */
    public static $environment;

    /**
     * The annotation reader instance singleton.
     *
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    public static $annotationReaderInstance;

    /**
     * Return's the annotation reader instance.
     *
     * @return \Doctrine\Common\Annotations\AnnotationReader
     */
    public function getAnnotationReader()
    {

        // query whether or not an instance already exists
        if (TimedObjectInvoker::$annotationReaderInstance === null) {
            TimedObjectInvoker::$annotationReaderInstance = new AnnotationReader();
        }

        // return the instance
        return TimedObjectInvoker::$annotationReaderInstance;
    }

    /**
     * Return's the method annotation with the passed name, if available.
     *
     * @param \AppserverIo\Lang\Reflection\MethodInterface $reflectionMethod The reflection method to return the annotation for
     * @param string                                       $annotationName   The name of the annotation to return
     *
     * @return object|null The method annotation, or NULL if not available
     */
    public function getMethodAnnotation(MethodInterface $reflectionMethod, $annotationName)
    {
        return $this->getAnnotationReader()->getMethodAnnotation($reflectionMethod->toPhpReflectionMethod(), $annotationName);
    }

    /**
     * Injects the timed object instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $timedObject The timed object instance
     *
     * @return void
     */
    public function injectTimedObject(ClassInterface $timedObject)
    {
        $this->timedObject = $timedObject;
    }

    /**
     * Injects the storage for the timeout methods.
     *
     * @param \AppserverIo\Storage\StorageInterface $timeoutMethods The storage for the timeout methods
     *
     * @return void
     */
    public function injectTimeoutMethods(StorageInterface $timeoutMethods)
    {
        $this->timeoutMethods = $timeoutMethods;
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
     * Return the timed object instance.
     *
     * @return \AppserverIo\Lang\Reflection\ClassInterface The timed object instance
     */
    public function getTimedObject()
    {
        return $this->timedObject;
    }

    /**
     * Returns the timeout methods.
     *
     * @return \AppserverIo\Storage\StorageInterface A collection of timeout methods
     **/
    public function getTimeoutMethods()
    {
        return $this->timeoutMethods;
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
     * The globally unique identifier for this timed object invoker.
     *
     * @return string
     */
    public function getTimedObjectId()
    {
        return $this->getTimedObject()->getShortName();
    }

    /**
     * Responsible for invoking the timeout method on the target object.
     *
     * The timerservice implementation invokes this method as a callback when a timeout occurs for the
     * passed timer. The timerservice implementation will be responsible for passing the correct
     * timeout method corresponding to the <code>timer</code> on which the timeout has occurred.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerInterface $timer         The timer that is passed to timeout
     * @param \AppserverIo\Lang\Reflection\MethodInterface    $timeoutMethod The timeout method
     *
     * @return void
     */
    public function callTimeout(TimerInterface $timer, MethodInterface $timeoutMethod = null)
    {

        try {
            // synchronize the application instance and register the class loaders
            $application = $this->getApplication();
            $application->registerClassLoaders();

            // register the applications annotation registries
            $application->registerAnnotationRegistries();

            // initialize the initial context instance
            $initialContext = new InitialContext();
            $initialContext->injectApplication($application);

            // add the application instance to the environment
            Environment::singleton()->setAttribute(EnvironmentKeys::APPLICATION, $application);

            // create s simulated request/session ID whereas session equals request ID
            Environment::singleton()->setAttribute(EnvironmentKeys::SESSION_ID, $sessionId = SessionUtils::generateRandomString());
            Environment::singleton()->setAttribute(EnvironmentKeys::REQUEST_ID, $sessionId);

            // log a message with the timed object information
            \info(sprintf('Now invoke timed object "%s" with session ID "%s"', $this->getTimedObjectId(), $sessionId));

            // lookup the enterprise bean using the initial context
            $instance = $initialContext->lookup($this->getTimedObjectId());

            // check if the timeout method has been passed
            if ($timeoutMethod != null) {
                // if yes, invoke it on the proxy
                $callback = array($instance, $timeoutMethod->getMethodName());
                call_user_func_array($callback, array($timer));
                return;
            }

            // check if we've a default timeout method
            if ($this->defaultTimeoutMethod != null) {
                // if yes, invoke it on the proxy
                $callback = array($instance, $this->defaultTimeoutMethod->getMethodName());
                call_user_func_array($callback, array($timer));
                return;
            }

        } catch (\Exception $e) {
            error_log($e->__toString());
        }
    }

    /**
     * Initializes the timed object invoker with the methods annotated
     * with the @Timeout or @Schedule annotation.
     *
     * @return void
     */
    public function start()
    {

        // create a reflection object instance of the timed object
        $reflectionClass = $this->getTimedObject();

        // first check if the bean implements the timed object interface => so we've a default timeout method
        if ($reflectionClass->implementsInterface('AppserverIo\Psr\EnterpriseBeans\TimedObjectInterface')) {
            $this->defaultTimeoutMethod = $reflectionClass->getMethod(TimedObjectInterface::DEFAULT_TIMEOUT_METHOD);
        }

        // check the methods of the bean for a @Timeout annotation => overwrite the default
        // timeout method defined by the interface
        /** @var \AppserverIo\Lang\Reflection\MethodInterface $timeoutMethod */
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $timeoutMethod) {
            // check if the timed object instance has @Timeout annotation => default timeout method
            if ($this->getMethodAnnotation($timeoutMethod, Timeout::class)) {
                $this->defaultTimeoutMethod = $timeoutMethod;
            }

            // check if the timed object instance has @Schedule annotation
            if ($this->getMethodAnnotation($timeoutMethod, Schedule::class)) {
                $this->timeoutMethods[$timeoutMethod->getMethodName()] = $timeoutMethod;
            }
        }
    }
}
