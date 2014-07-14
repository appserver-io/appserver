<?php

/**
 * TechDivision\ApplicationServer\GenericDeployment
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Storage\StackableStorage;
use TechDivision\Application\Application;
use TechDivision\ServletEngine\DefaultSessionSettings;
use TechDivision\ServletEngine\PersistentSessionManager;
use TechDivision\ServletEngine\StandardSessionManager;
use TechDivision\ServletEngine\Authentication\StandardAuthenticationManager;
use TechDivision\ServletEngine\StandardSessionMarshaller;
use TechDivision\ServletEngine\SessionFactory;
use TechDivision\ServletEngine\FilesystemPersistenceManager;
use TechDivision\ServletEngine\StandardGarbageCollector;
use TechDivision\ServletEngine\ServletLocator;
use TechDivision\ServletEngine\ServletManager;
use TechDivision\MessageQueue\QueueManager;
use TechDivision\MessageQueue\QueueLocator;
use TechDivision\PersistenceContainer\BeanManager;
use TechDivision\PersistenceContainer\BeanLocator;
use TechDivision\WebSocketServer\HandlerManager;
use TechDivision\WebSocketServer\HandlerLocator;
use TechDivision\ApplicationServer\AbstractDeployment;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

/**
 * Specific deployment implementation for web applications.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class GenericDeployment extends AbstractDeployment
{

    /**
     * Initializes the available applications and adds them to the deployment instance.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface The container we want to add the applications to
     *
     * @return void
     */
    public function deploy(ContainerInterface $container)
    {

        // gather all the deployed web applications
        foreach (new \FilesystemIterator($container->getAppBase()) as $folder) {

            // prepare directories for web and persistence container specific deployment descriptors
            $webInf = new \SplFileInfo($folder . DIRECTORY_SEPARATOR . 'WEB-INF');
            $metaInf = new \SplFileInfo($folder . DIRECTORY_SEPARATOR . 'META-INF');

            // check if file or subdirectory has been found and a deployment descriptor is available
            if ($folder->isDir() && ($webInf->isDir() || $metaInf->isDir())) {

                // initialize the application instance
                $application = new Application();

                // initialize the generic instances and information
                $application->injectInitialContext($this->getInitialContext());
                $application->injectAppBase($container->getAppBase());
                $application->injectBaseDirectory($container->getBaseDirectory());
                $application->injectName($folder->getBasename());

                // add the default class loader
                $application->addClassLoader($this->getInitialContext()->getClassLoader());

                // if we found a WEB-INF directory, we've to initialize the web container specific managers
                if ($webInf->isDir()) {

                    // initialize servlet + session manager
                    $servletManager = $this->getServletContext($folder);
                    $sessionManager = $this->getSessionManager();
                    $sessionManager->injectServletManager($servletManager);

                    // init the application with necessary managers
                    $application->addManager($servletManager);
                    $application->addManager($sessionManager);
                    $application->addManager($this->getAuthenticationManager());
                    $application->addManager($this->getHandlerManager($folder));
                }

                // if we found a META-INF directory, we've to initialize the persistence container specific managers
                if ($metaInf->isDir()) {
                    $application->addManager($this->getBeanManager($folder));
                    $application->addManager($this->getQueueManager($folder));
                }

                // add the application to the available applications
                $container->addApplication($application);
            }
        }
    }

    /**
     * Creates and returns a new servlet context that handles the servlets
     * found in the passe web application folder.
     *
     * @param \SplFileInfo $folder The folder with the web application
     *
     * @return \TechDivision\ServletEngine\ServletManager The initialized servlet context
     */
    protected function getServletContext(\SplFileInfo $folder)
    {
        $servletContext = new ServletManager();
        $servletContext->injectWebappPath($folder->getPathname());
        $servletContext->injectResourceLocator($this->getResourceLocator());
        return $servletContext;
    }

    /**
     * Creates and returns a new resource locator to locate the servlet that
     * has to handle a request.
     *
     * @return \TechDivision\ServletEngine\ServletLocator The resource locator instance
     */
    protected function getResourceLocator()
    {
        return new ServletLocator();
    }

    /**
     * Creates and returns a new handler manager that handles the handler
     * found in the passed web application folder.
     *
     * @param \SplFileInfo $folder The folder with the web application
     *
     * @return \TechDivision\WebSocketServer\HandlerManager The initialized handler manager
     */
    protected function getHandlerManager(\SplFileInfo $folder)
    {
        $handlerManager = new HandlerManager();
        $handlerManager->injectWebappPath($folder->getPathname());
        $handlerManager->injectHandlerLocator($this->getHandlerLocator());
        return $handlerManager;
    }

    /**
     * Creates and returns a new handler locator to locate the handler that
     * has to handle a request.
     *
     * @return \TechDivision\WebSocketServer\HandlerLocator The handler locator instance
     */
    protected function getHandlerLocator()
    {
        return new HandlerLocator();
    }

    /**
     * Creates and returns a new bean manager that handles the beans
     * found in the passed web application folder.
     *
     * @param \SplFileInfo $folder The folder with the web application
     *
     * @return \TechDivision\PersistenceContainer\BeanManager The initialized bean manager
     */
    protected function getBeanManager(\SplFileInfo $folder)
    {
        $beanManager = new BeanManager();
        $beanManager->injectWebappPath($folder->getPathname());
        $beanManager->injectResourceLocator($this->getBeanLocator());
        return $beanManager;
    }

    /**
     * Creates and returns a new bean locator to locate the beans that
     * has to handle a request.
     *
     * @return \TechDivision\PersistenceContainer\BeanLocator The bean locator instance
     */
    protected function getBeanLocator()
    {
        return new BeanLocator();
    }

    /**
     * Returns an initialized session manager instance.
     *
     * @return \TechDivision\ServletEngine\SessionManager The session manager instance
     */
    protected function getSessionManager()
    {

        // load the system configuration
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // initialize the session pool
        $sessions = new StackableStorage();
        $checksums = new StackableStorage();
        $sessionPool = new StackableStorage();
        $sessionSettings = new DefaultSessionSettings();
        $sessionMarshaller = new StandardSessionMarshaller();

        // we need a session factory instance
        $sessionFactory = new SessionFactory($sessionPool);
        $sessionFactory->start();

        // we need a persistence manager and garbage collector
        $persistenceManager = new FilesystemPersistenceManager();
        $persistenceManager->injectSessions($sessions);
        $persistenceManager->injectChecksums($checksums);
        $persistenceManager->injectSessionSettings($sessionSettings);
        $persistenceManager->injectSessionMarshaller($sessionMarshaller);
        $persistenceManager->injectSessionFactory($sessionFactory);
        $persistenceManager->injectUser($systemConfiguration->getParam('user'));
        $persistenceManager->injectGroup($systemConfiguration->getParam('group'));
        $persistenceManager->injectUmask($systemConfiguration->getParam('umask'));
        $persistenceManager->start();

        // we need a garbage collector
        $garbageCollector = new StandardGarbageCollector();
        $garbageCollector->injectSessions($sessions);
        $garbageCollector->injectSessionSettings($sessionSettings);
        $garbageCollector->start();

        // and finally we need the session manager instance
        $sessionManager = new StandardSessionManager();
        $sessionManager->injectSessions($sessions);
        $sessionManager->injectSessionSettings($sessionSettings);
        $sessionManager->injectSessionFactory($sessionFactory);
        $sessionManager->injectPersistenceManager($persistenceManager);
        $sessionManager->injectGarbageCollector($garbageCollector);

        // return the session manager instance
        return $sessionManager;
    }

    /**
     * Returns the authentication manager.
     *
     * @return \TechDivision\ServletEngine\Authentication\AuthenticationManager
     */
    protected function getAuthenticationManager()
    {
        return new StandardAuthenticationManager();
    }

    /**
     * Creates and returns a new queue manager that handles the queue
     * found in the passed application folder.
     *
     * @param \SplFileInfo $folder The folder with the application
     *
     * @return \TechDivision\MessageQueue\QueueManager The initialized queue manager
     */
    protected function getQueueManager(\SplFileInfo $folder)
    {
        $queueManager = new QueueManager();
        $queueManager->injectWebappPath($folder->getPathname());
        $queueManager->injectResourceLocator($this->getQueueLocator());
        return $queueManager;
    }

    /**
     * Creates and returns a new handler locator to locate the handler that
     * has to handle a request.
     *
     * @return \TechDivision\MessageQueue\Service\Locator\Locator The handler locator instance
     */
    protected function getQueueLocator()
    {
        return new QueueLocator();
    }
}
