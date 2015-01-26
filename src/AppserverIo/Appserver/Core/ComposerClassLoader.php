<?php

/**
 * AppserverIo\Appserver\Core\ComposerClassLoader
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use Composer\Autoload\ClassLoader;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface;
use AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface;

/**
 * A Wrapper for the web application specific composer class loader.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
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
