<?php

/**
 * \AppserverIo\Appserver\Core\FileClassLoaderFactory
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
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ClassLoaderFactoryInterface;

/**
 * A factory for the simple  file class loader instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FileClassLoaderFactory implements ClassLoaderFactoryInterface
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

        // initialize the array with the configured directories
        $directories = array();

        // load the composer class loader for the configured directories
        /** @var \AppserverIo\Appserver\Core\Api\Node\DirectoryNode $directory */
        foreach ($configuration->getDirectories() as $directory) {
            // we prepare the directories to include files AFTER registering (in application context)
            $directories[] = $directory->getNodeValue();
        }

        // attach the class loader instance
        $application->addClassLoader(new FileClassLoader($directories), $configuration);
    }
}
