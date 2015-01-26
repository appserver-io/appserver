<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\StandardGarbageCollector
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

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\EnterpriseBeans\BeanContext;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The garbage collector for the stateful session beans.
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
class StandardGarbageCollector extends \Thread
{

    /**
     * The time we wait after each loop.
     *
     * @var integer
     */
    const TIME_TO_LIVE = 1;

    /**
     * Initializes the queue worker with the application and the storage it should work on.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationInterface $application The application instance with the queue manager/locator
     */
    public function __construct(ApplicationInterface $application)
    {

        // bind the gc to the application
        $this->application = $application;

        // start the worker
        $this->start();
    }

    /**
     * We process the messages here.
     *
     * @return void
     */
    public function run()
    {

        // create a local instance of appication and storage
        $application = $this->application;

        // register the class loader again, because each thread has its own context
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($profileLogger = $application->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $profileLogger->appendThreadContext('persistence-container-garbage-collector');
        }

        while (true) {
            // wait one second
            $this->synchronized(function () {
                $this->wait(1000000 * StandardGarbageCollector::TIME_TO_LIVE);
            });

            // we need the bean manager that handles all the beans
            $beanManager = $application->search('BeanContext');

            // load the map with the stateful session beans
            $statefulSessionBeans = $beanManager->getStatefulSessionBeans();

            // iterate over the applications sessions with stateful session beans
            foreach ($statefulSessionBeans as $sessionId => $sessions) {
                // query if we've a map with stateful session beans
                if ($sessions instanceof StatefulSessionBeanMap) {
                    // initialize the timestamp with the actual time
                    $actualTime = time();

                    // check the lifetime of the stateful session beans
                    foreach ($sessions->getLifetime() as $className => $lifetime) {
                        if ($lifetime < $actualTime) {
                            // if the stateful session bean has timed out, remove it
                            $beanManager->removeStatefulSessionBean($sessionId, $className);
                        }
                    }
                }
            }

            if ($profileLogger) {
                // profile the stateful session bean map size
                $profileLogger->debug(
                    sprintf('Processed standard garbage collector, handling %d SFSBs', sizeof($statefulSessionBeans))
                );
            }
        }
    }
}
