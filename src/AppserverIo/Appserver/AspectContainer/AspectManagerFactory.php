<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Appserver\AspectContainer;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;

/**
 * Factory which allows for the injection of an aspect manager into an application based
 * on the visitor pattern.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */
class AspectManagerFactory
{
    /**
     * The main method that creates new instances in a separate context.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface $application          The application instance to register the class loader with
     * @param \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface                                          $managerConfiguration The manager configuration
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ManagerNodeInterface $managerConfiguration)
    {

        // check if the correct autoloader has been registered, if so we have to get its aspect register.
        // if not we have to fail here
        $classLoader = $application->search('DgClassLoader');
        $aspectRegister = $classLoader->getAspectRegister();

        // initialize the aspect manager
        $aspectManager = new AspectManager();
        $aspectManager->injectApplication($application);
        $aspectManager->injectAspectRegister($aspectRegister);

        // attach the instance
        $application->addManager($aspectManager, $managerConfiguration);
    }
}
