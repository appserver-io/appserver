<?php

/**
 * TechDivision\ApplicationServer\GenericClassLoader
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\PBC\Config;
use TechDivision\PBC\AutoLoader;
use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Interfaces\ClassLoaderInterface;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface;

/**
 * This is a generic class loader implemenation.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class GenericClassLoader extends AutoLoader implements ClassLoaderInterface
{

    /**
     * The unique class loader identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'generic';

    /**
     * Factory method that adds a initialized class loader to the passed application.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface         $application   The application instance
     * @param \TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface $configuration The class loader configuration node
     *
     * @return void
     */
    public static function get(ApplicationInterface $application, ClassLoaderNodeInterface $configuration = null)
    {

        // load the web application path we want to register the class loader for
        $webappPath = $application->getWebappPath();

        // initialize the class path and the enforcement directories
        $classPath = array();
        $enforcementDirs = array();

        // add the possible class path if folder is available
        foreach ($configuration->getDirectories() as $directory) {
            if (is_dir($webappPath . $directory->getNodeValue())) {
                array_push($classPath, $webappPath . $directory->getNodeValue());
                if ($directory->isEnforced()) {
                    array_push($enforcementDirs, $webappPath . $directory->getNodeValue());
                }
            }
        }

        // initialize the class loader configuration
        $config = new Config();

        // set the environment mode we want to use
        $config->setValue('environment', $configuration->getEnvironment());

        // set the cache directory
        $config->setValue('cache/dir', $application->getCacheDir());

        // set the default autoloader values
        $config->setValue('autoloader/dirs', $classPath);

        // set the default enforcement configuration values
        $config->setValue('enforcement/dirs', $enforcementDirs);
        $config->setValue('enforcement/enforce-default-type-safety', $configuration->getTypeSafety());
        $config->setValue('enforcement/processing', $configuration->getProcessing());
        $config->setValue('enforcement/level', $configuration->getEnforcementLevel());
        $config->setValue('enforcement/logger', $application->getInitialContext()->getSystemLogger());

        // create the autoloader instance and fill the structure map
        $autoLoader = new GenericClassLoader($config);
        $autoLoader->getStructureMap()->fill();

        // add the class loader instance to the application
        $application->addClassLoader($autoLoader);
    }
}
