<?php

/**
 * AppserverIo\Appserver\Core\ComposerClassLoaderFactory
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
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface;

/**
 * A factory for the composer class loader instances.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ComposerClassLoaderFactory
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

        // load the application directory
        $webappPath = $application->getWebappPath();

        // initialize the array with the configured directories
        $directories = array();

        // load the composer class loader for the configured directories
        foreach ($configuration->getDirectories() as $directory) {
            // we prepare the directories to include scripts AFTER registering (in application context)
            $directories[] = $webappPath . $directory->getNodeValue();

            // check if an autoload.php is available
            if (file_exists($webappPath . $directory->getNodeValue() . DIRECTORY_SEPARATOR . 'autoload.php')) {
                // if yes, we try to instanciate a new class loader instance
                $classLoader = new ComposerClassLoader($directories);

                // set the composer include paths
                if (file_exists($webappPath . $directory->getNodeValue() . '/composer/include_paths.php')) {
                    $includePaths = require $webappPath . $directory->getNodeValue() . '/composer/include_paths.php';
                    array_push($includePaths, get_include_path());
                    set_include_path(join(PATH_SEPARATOR, $includePaths));
                }

                // add the composer namespace declarations
                if (file_exists($webappPath . $directory->getNodeValue() . '/composer/autoload_namespaces.php')) {
                    $map = require $webappPath . $directory->getNodeValue() . '/composer/autoload_namespaces.php';
                    foreach ($map as $namespace => $path) {
                        $classLoader->set($namespace, $path);
                    }
                }

                // add the composer PSR-4 compatible namespace declarations
                if (file_exists($webappPath . $directory->getNodeValue() . '/composer/autoload_psr4.php')) {
                    $map = require $webappPath . $directory->getNodeValue() . '/composer/autoload_psr4.php';
                    foreach ($map as $namespace => $path) {
                        $classLoader->setPsr4($namespace, $path);
                    }
                }

                // add the composer class map
                if (file_exists($webappPath . $directory->getNodeValue() . '/composer/autoload_classmap.php')) {
                    $classMap = require $webappPath . $directory->getNodeValue() . '/composer/autoload_classmap.php';
                    if ($classMap) {
                        $classLoader->addClassMap($classMap);
                    }
                }

                // attach the class loader instance
                $application->addClassLoader($classLoader, $configuration);
            }
        }
    }
}
