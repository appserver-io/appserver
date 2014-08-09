<?php

/**
 * TechDivision\ApplicationServer\ComposerClassLoader
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

use Composer\Autoload\ClassLoader;
use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Interfaces\ClassLoaderInterface;
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
class ComposerClassLoader extends ClassLoader implements ClassLoaderInterface
{

    /**
     * The unique class loader identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'composer';

    /**
     * The array with the configured directories.
     *
     * @param array
     */
    protected $directories;

    /**
     * Initialize the class loader with the configured directories.
     *
     * @param array $directories The array with the configured directories
     */
    public function __construct(array $directories = array())
    {
        $this->directories = $directories;
    }

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

                // add the class loader instance
                $application->addClassLoader($classLoader);
            }
        }
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @param bool $throw   If register should throw an exception or not
     * @param bool $prepend If register should prepend
     *
     * @return void
     */
    public function register($throw = true, $prepend = false)
    {

        // register the class loader instance
        parent::register($prepend);

        // require the files registered with composer (e. g. Swift Mailer)
        foreach ($this->directories as $directory) {
            if (file_exists($directory . '/composer/autoload_files.php')) {
                $includeFiles = require $directory . '/composer/autoload_files.php';
                foreach ($includeFiles as $file) {
                    require_once $file;
                }
            }
        }
    }
}
