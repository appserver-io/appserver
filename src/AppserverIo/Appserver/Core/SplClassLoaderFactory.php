<?php

/**
 * AppserverIo\Appserver\Core\SplClassLoaderFactory
 *
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
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface;

/**
 * A factory for the Doppelgaenger class loader instances.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SplClassLoaderFactory
{

    /**
     * Visitor method that registers the class loaders in the application.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface             $application   The application instance to register the class loader with
     * @param \AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface $configuration The class loader configuration
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ClassLoaderNodeInterface $configuration)
    {

        // initialize the storage for the class map and the include path
        $classMap = new GenericStackable();
        $includePath = array();

        // load the application directory
        $webappPath = $application->getWebappPath();

        // load the composer class loader for the configured directories
        foreach ($configuration->getDirectories() as $directory) {
            // we prepare the directories to include scripts AFTER registering (in application context)
            $includePath[] = $webappPath . $directory->getNodeValue();
        }

        // initialize and return the SPL class loader instance
        $application->addClassLoader(new SplClassLoader($classMap, $includePath), $configuration);
    }
}
