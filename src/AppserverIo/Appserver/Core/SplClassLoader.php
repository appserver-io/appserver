<?php

/**
 * AppserverIo\Appserver\Core\SplClassLoader
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface;
use AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface;

/**
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * @author    Jonathan H. Wage <jonwage@gmail.com>
 * @author    Roman S. Borschel <roman@code-factory.org>
 * @author    Matthew Weier O'Phinney <matthew@zend.com>
 * @author    Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author    Fabien Potencie <fabien.potencier@symfony-project.org>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SplClassLoader implements ClassLoaderInterface
{

    /**
     * The unique class loader identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'SplClassLoader';

    /**
     * Visitor method that adds a initialized class loader to the passed application.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface             $application   The application instance
     * @param \AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface $configuration The class loader configuration node
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ClassLoaderNodeInterface $configuration = null)
    {
        $application->addClassLoader(SplClassLoader::factory());
    }

    /**
     * Simple factory method to create a new instance of the SplClassLoader.
     *
     * @return \AppserverIo\Appserver\Core\SplClassLoader The class loader instance
     */
    public static function factory()
    {

        // initialize the storage for the class map and the include path
        $classMap = new GenericStackable();
        $includePath = array();

        // initialize and return the SPL class loader instance
        return new SplClassLoader($classMap, $includePath);
    }

    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes of the specified
     * namespace and searches for the class files in the include paths passed as
     * array.
     *
     * @param \AppserverIo\Storage\GenericStackable $classMap           The storage for the class map
     * @param \AppserverIo\Storage\GenericStackable $includePath        The storage for the include path
     * @param string                                $namespace          The namespace to use
     * @param string                                $namespaceSeparator The namespace separator
     * @param string                                $fileExtension      The filename extension
     */
    public function __construct($classMap, $includePath, $namespace = null, $namespaceSeparator = '\\', $fileExtension = '.php')
    {

        // initialize the member variables
        $this->classMap = $classMap;
        $this->namespace = $namespace;
        $this->includePath = $includePath;
        $this->fileExtension = $fileExtension;
        $this->namespaceSeparator = $namespaceSeparator;

        // initialize an array for the include paths
        $paths = array();

        // initialize the default include path
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $val) {
            if (empty($val) === false) {
                $paths[] = $val;
            }
        }

        // set the include paths
        $this->includePath = $paths;
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
        spl_autoload_register(array($this, 'loadClass'), $throw, $prepend);
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     *
     * @return void
     * @todo Has to be refactored to improve performance
     */
    public function loadClass($className)
    {

        // backup the requested class name
        $requestedClassName = $className;

        // check if the requested class name has already been loaded
        if (isset($this->classMap[$requestedClassName]) !== false) {
            require $this->classMap[$requestedClassName];
            return true;
        }

        // concatenate namespace and separator
        $namespaceAndSeparator = $this->namespace . $this->namespaceSeparator;

        // if a namespace is available OR the classname contains a namespace
        if ($namespaceAndSeparator === substr($className, 0, strlen($namespaceAndSeparator)) || $this->namespace === null) {
            // initialize filename, classname and namespace
            $fileName = '';
            $namespace = '';
            if (($lastNsPos = strripos($className, $this->namespaceSeparator)) !== false) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            // prepare filename
            $fileName .= $className . $this->fileExtension;

            // try to load the requested class
            foreach ($this->includePath as $includePath) {
                $toRequire = $includePath . DIRECTORY_SEPARATOR . $fileName;
                $psr4FileName = $includePath . DIRECTORY_SEPARATOR . ltrim(strstr(ltrim(strstr($fileName, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

                if (file_exists($toRequire)) {
                    // add the found file to the class map
                    $this->classMap[$requestedClassName] = $toRequire;
                    // require the file and return TRUE
                    require $toRequire;
                    return true;

                } elseif (file_exists($psr4FileName) && !is_dir($psr4FileName)) {
                    // add the found file to the class map
                    $this->classMap[$requestedClassName] = $psr4FileName;
                    // require the file and return TRUE
                    require $psr4FileName;
                    return true;
                }
            }
        }

        // return FALSE, because the class loader can't require the requested class name
        return false;
    }
}
