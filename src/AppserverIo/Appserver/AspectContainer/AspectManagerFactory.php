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
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */

namespace AppserverIo\Appserver\AspectContainer;

use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * AppserverIo\Appserver\AspectContainer\AspectManagerFactory
 *
 * Factory which allows for the injection of an aspect manager into an application based on the visitor pattern
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */
class AspectManagerFactory
{
    /**
     * The main method that creates new instances in a separate context.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface                           $application          The application instance to register the class loader with
     * @param \AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface $managerConfiguration The manager configuration
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ManagerConfigurationInterface $managerConfiguration)
    {

        // check if the correct autoloader has been registered, if so we have to get its aspect register.
        // if not we have to fail here
        $classLoader = $application->search('DgClassLoader');
        $aspectRegister = $classLoader->getAspectRegister();

        // initialize the aspect manager
        $aspectManager = new AspectManager();
        $aspectManager->injectApplication($application);
        $aspectManager->injectAspectRegister($aspectRegister);
        $aspectManager->injectWebappPath($application->getWebappPath());

        // attach the instance
        $application->addManager($aspectManager, $managerConfiguration);
    }
}
