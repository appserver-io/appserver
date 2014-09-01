<?php

/**
 * TechDivision\ApplicationServer\SplClassLoader
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Application\Interfaces\ContextInterface;
use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface;

/**
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * http://groups.google.com/group/php-standards/web/final-proposal
 *
 *     // Example which loads classes for the Doctrine Common package in the
 *     // Doctrine\Common namespace.
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Jonathan H. Wage <jonwage@gmail.com>
 * @author    Roman S. Borschel <roman@code-factory.org>
 * @author    Matthew Weier O'Phinney <matthew@zend.com>
 * @author    Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author    Fabien Potencie <fabien.potencier@symfony-project.org>
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class SplClassLoader extends \Stackable
{

    /**
     * The unique class loader identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'spl';

    /**
     * The unique key to store the class map in the initial context.
     *
     * @var string
     */
    const CLASS_MAP = 'SplClassLoader.classMap';

    /**
     * Visitor method that adds a initialized class loader to the passed application.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface         $application   The application instance
     * @param \TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface $configuration The class loader configuration node
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ClassLoaderNodeInterface $configuration = null)
    {

        // load the web application path we want to register the class loader for
        $webappPath = $application->getWebappPath();

        // initialize the array with the applications additional include paths
        $includePath = array();

        // add the possible class path if folder is available
        foreach ($configuration->getDirectories() as $directory) {
            if (is_dir($webappPath . $directory->getNodeValue())) {
                array_push($includePath, $webappPath . $directory->getNodeValue());
            }
        }

        // initialize the SPL class loader instance
        $classLoader = new SplClassLoader($application->getInitialContext(), null, $includePath);

        // add the class loader instance
        $application->addClassLoader($classLoader);
    }

    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes of the specified
     * namespace and searches for the class files in the include paths passed as
     * array.
     *
     * @param \TechDivision\Application\Interfaces\ContextInterface $initialContext     The initial context instance
     * @param string                                                $namespace          The namespace to use
     * @param array                                                 $includePath        The include path to use
     * @param string                                                $namespaceSeparator The namespace separator
     * @param string                                                $fileExtension      The filename extension
     */
    public function __construct(ContextInterface $initialContext, $namespace = null, array $includePath = null, $namespaceSeparator = '\\', $fileExtension = '.php')
    {
        // set the initial context and initialize the class map
        $this->initialContext = $initialContext;
        $this->getInitialContext()->setAttribute(self::CLASS_MAP, array());

        // ATTENTION: Don't delete this, it's necessary because this IS a \Stackable
        $this->fileExtension = $fileExtension;
        $this->namespaceSeparator = $namespaceSeparator;

        // set namespace and initialize include path
        $this->namespace = $namespace;

        // initialize the include path with the defined include path
        $this->includePath = new GenericStackable();
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $val) {
            if (!empty($val)) {
                $this->includePath[] = $val;
            }
        }

        // add the directories passed as parameter
        if ($includePath != null) {
            foreach ($includePath as $val) {
                $this->includePath[] = $val;
            }
        }
    }

    /**
     * Gets the namespace seperator used by classes in the namespace of this class loader.
     *
     * @return void
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return array $includePath
     */
    public function getIncludePath()
    {
        return $this->includePath;
    }

    /**
     * Returns the initial context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Gets the file extension of class files in the namespace of this class loader.
     *
     * @return string $fileExtension
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
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

        // try to load the class map from the inital context
        $classMap = $this->getInitialContext()->getAttribute(self::CLASS_MAP);

        // check if the requested class name has already been loaded
        if (isset($classMap[$requestedClassName]) !== false) {
            require $classMap[$requestedClassName];
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
            foreach ($this->getIncludePath() as $includePath) {
                $toRequire = $includePath . DIRECTORY_SEPARATOR . $fileName;
                if (file_exists($toRequire)) {
                    // add the found file to the class map
                    $classMap[$requestedClassName] = $toRequire;
                    $this->getInitialContext()->setAttribute(self::CLASS_MAP, $classMap);
                    // require the file and return TRUE
                    require $toRequire;
                    return true;
                }
            }
        }

        // return FALSE, because the class loader can't require the requested class name
        return false;
    }
}
