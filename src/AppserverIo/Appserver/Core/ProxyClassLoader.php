<?php

/**
 * \AppserverIo\Appserver\Core\ProxyClassLoader
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

use AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface;

/**
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2017 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ProxyClassLoader implements ClassLoaderInterface
{

    /**
     * The unique class loader identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'ProxyClassLoader';

    /**
     * The include path to use.
     *
     * @var string
     */
    protected $includePath = null;

    /**
     * The namespace to use.
     *
     * @var string
     */
    protected $namespace = null;

    /**
     * The namespace separator to use.
     *
     * @var string
     */
    protected $namespaceSeparator = '\\';

    /**
     * The file extension to use.
     *
     * @var string
     */
    protected $fileExtension = '.php';

    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes of the specified
     * namespace and searches for the class files in the include paths passed as
     * array.
     *
     * @param string $includePath The storage for the include path
     */
    public function __construct($includePath)
    {
        $this->includePath = $includePath;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @param bool $throw   If register should throw an exception or not
     * @param bool $prepend If register should prepend
     *
     * @return void
     */
    public function register($throw = true, $prepend = true)
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
     */
    public function loadClass($className)
    {

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
            $toRequire = $this->includePath . DIRECTORY_SEPARATOR . $fileName;
            $psr4FileName = $this->includePath . DIRECTORY_SEPARATOR . ltrim(strstr(ltrim(strstr($fileName, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

            // query whether or tno the file exsists
            if (file_exists($toRequire)) {
                // require the file and return TRUE
                require $toRequire;
                return true;

            } elseif (file_exists($psr4FileName) && !is_dir($psr4FileName)) {
                // require the file and return TRUE
                require $psr4FileName;
                return true;
            }
        }
    }
}
