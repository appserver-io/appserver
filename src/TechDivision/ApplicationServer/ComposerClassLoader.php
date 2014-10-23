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
 * A Wrapper for the web application specific composer class loader.
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
