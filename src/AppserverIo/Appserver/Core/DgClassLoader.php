<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface;
use AppserverIo\Doppelgaenger\AspectRegister;
use AppserverIo\Doppelgaenger\CacheMap;
use AppserverIo\Doppelgaenger\Dictionaries\Placeholders;
use AppserverIo\Doppelgaenger\Entities\Definitions\Structure;
use AppserverIo\Doppelgaenger\Generator;
use AppserverIo\Doppelgaenger\StructureMap;
use AppserverIo\Doppelgaenger\Config;
use AppserverIo\Doppelgaenger\AutoLoader;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * This class is used to delegate to doppelgaenger's autoloader.
 * This is needed as our multi-threaded environment would not allow any out-of-the-box code generation
 * in an on-the-fly manner.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DgClassLoader extends \Stackable implements ClassLoaderInterface
{

    /**
     * Our default configuration file
     *
     * @const string CONFIG_FILE
     */
    const CONFIG_FILE = 'pbc.conf.json';

    /**
     * The amount of structures we will generate per thread
     *
     * @const int GENERATOR_STACK_COUNT
     */
    const GENERATOR_STACK_COUNT = 5;

    /**
     * The unique class loader identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'doppelgaenger';

    /**
     * The name of our autoload method.
     *
     * @const string OUR_LOADER
     */
    const OUR_LOADER = 'loadClass';

    /**
     * The name of our simple/quick autoload method.
     *
     * @const string OUR_LOADER_PROD_NO_OMIT
     */
    const OUR_LOADER_PROD_NO_OMIT = 'loadClassProductionNoOmit';

    /**
     * Default constructor
     *
     * We will do all the necessary work to load without any further hassle.
     * Will check if there is content in the cache directory.
     * If not we will parse anew.
     *
     * @param \AppserverIo\Doppelgaenger\Config|null $config An already existing config instance
     */
    public function __construct(Config $config = null)
    {
        // If we got a config we can use it, if not we will get a context less config instance
        if (is_null($config)) {

            $config = new Config();
            $config->load(APPSERVER_BP . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . self::CONFIG_FILE);

        } else {

            $this->config = $config;
        }

        // pre-load the configuration values
        $this->cacheDir =  $this->config->getValue('cache/dir');
        $this->environment = $this->config->getValue('environment');
        $this->autoloaderOmit = $this->config->hasValue('autoloader/omit');

        // Now that we got the config we can create a structure map to load from
        $this->structureMap = new StackableStructureMap(
            $this->getConfig()->getValue('autoloader/dirs'),
            $this->getConfig()->getValue('enforcement/dirs'),
            $this->getConfig()
        );

        $this->cache = null;
        $this->aspectRegister = new AspectRegister();

        // We now have a structure map instance, so fill it initially
        $this->getStructureMap()->fill();
    }

    /**
     * Will start the generation of the cache based on the known structures
     *
     * @return null
     */
    public function createCache()
    {
        // Check if there are files in the cache.
        // If not we will fill the cache, if there are we will check if there have been any changes to the enforced dirs
        $fileIterator = new \FilesystemIterator($this->getConfig()->getValue('cache/dir'), \FilesystemIterator::SKIP_DOTS);
        if (iterator_count($fileIterator) <= 1 || $this->getConfig()->getValue('environment') === 'development') {

            // Fill the cache
            $this->fillCache();

        } else {

            if (!$this->structureMap->isRecent()) {

                $this->refillCache();

            }
        }
    }

    /**
     * Creates the definitions by given structure map
     *
     * @return bool
     */
    protected function createDefinitions()
    {
        // Get all the structures we found
        $structures = $this->structureMap->getEntries(true, true);

        // We will need a CacheMap instance which we can pass to the generator
        // We need the caching configuration
        $cacheMap = new CacheMap($this->getConfig()->getValue('cache/dir'), array(), $this->getConfig());

        // We need a generator so we can create our proxies initially
        $generator = new Generator($this->structureMap, $cacheMap, $this->getConfig(), $this->getAspectRegister());

        // Iterate over all found structures and generate their proxies, but ignore the ones with omitted
        // namespaces
        $omittedNamespaces = array();
        if ($this->getConfig()->hasValue('autoloader/omit')) {

            $omittedNamespaces = $this->getConfig()->getValue('autoloader/omit');
        }

        // Now check which structures we have to create and split them up for multi-threaded creation
        $generatorStack = array();
        foreach ($structures as $identifier => $structure) {

            // Working on our own files has very weird side effects, so don't do it
            if (strpos($structure->getIdentifier(), 'AppserverIo\Doppelgaenger') !== false || !$structure->isEnforced()) {

                continue;
            }

            // Might this be an omitted structure after all?
            foreach ($omittedNamespaces as $omittedNamespace) {

                // If our class name begins with the omitted part e.g. it's namespace
                if (strpos($structure->getIdentifier(), $omittedNamespace) === 0) {

                    continue 2;
                }
            }

            // Fill it into the generator stack
            $generatorStack[$identifier] = $structure;
        }

        // Chuck the stack and start generating
        $generatorStack = array_chunk($generatorStack, self::GENERATOR_STACK_COUNT, true);

        // Generate all the structures!
        $generatorThreads = array();
        foreach ($generatorStack as $key => $generatorStackChunk) {

            $generatorThreads[$key] = new GeneratorThread($generator, $generatorStackChunk);
            $generatorThreads[$key]->start();
        }

        // Wait on the threads
        foreach ($generatorThreads as $generatorThread) {

            $generatorThread->join();
        }

        // Still here? Sounds about right
        return true;
    }

    /**
     * Will initiate the creation of a structure map and the parsing process of all found structures
     *
     * @return bool
     */
    protected function fillCache()
    {
        // Lets create the definitions
        return $this->createDefinitions();
    }

    /**
     * Getter for the $aspectRegister property
     *
     * @return \AppserverIo\Doppelgaenger\AspectRegister
     */
    public function getAspectRegister()
    {
        return $this->aspectRegister;
    }

    /**
     * Getter for the config member
     *
     * @return \AppserverIo\Doppelgaenger\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Getter for the structureMap member
     *
     * @return \AppserverIo\Doppelgaenger\StructureMap
     */
    public function getStructureMap()
    {
        return $this->structureMap;
    }

    /**
     * Will inject an AspectRegister instance into the generator
     *
     * @param \AppserverIo\Doppelgaenger\AspectRegister $aspectRegister The AspectRegister instance to inject
     *
     * @return null
     */
    public function injectAspectRegister(AspectRegister $aspectRegister)
    {
        $this->aspectRegister = $aspectRegister;
    }

    /**
     * Will load any given structure based on it's availability in our structure map which depends on the configured
     * project directories.
     *
     * If the structure cannot be found we will redirect to the composer autoloader which we registered as a fallback
     *
     * @param string $className The name of the structure we will try to load
     *
     * @return boolean
     */
    public function loadClassProductionNoOmit($className)
    {

        // prepare the cache path
        $cachePath = $this->cacheDir . DIRECTORY_SEPARATOR . str_replace('\\', '_', $className) . '.php';

        // check if the file is readable
        if (is_readable($cachePath)) {

            require $cachePath;
            return true;
        }

        // Still here? That sounds like bad news!
        return false;
    }

    /**
     * Will load any given structure based on it's availability in our structure map which depends on the configured
     * project directories.
     *
     * If the structure cannot be found we will redirect to the composer autoloader which we registered as a fallback
     *
     * @param string $className The name of the structure we will try to load
     *
     * @return boolean
     */
    public function loadClass($className)
    {

        // Might the class be a omitted one? If so we can require the original.
        if ($this->autoloaderOmit) {

            $omittedNamespaces = $this->config->getValue('autoloader/omit');
            foreach ($omittedNamespaces as $omitted) {

                // If our class name begins with the omitted part e.g. it's namespace
                if (strpos($className, str_replace('\\\\', '\\', $omitted)) === 0) {
                    return false;
                }
            }
        }

        // Do we have the file in our cache dir? If we are in development mode we have to ignore this.
        if ($this->environment !== 'development') {
            if ($this->loadClassProductionNoOmit($className) === true) {
                return true;
            }
        }

        // If we are loading something of our own library we can skip to composer
        if (strpos($className, 'AppserverIo\Doppelgaenger') === 0 || strpos($className, 'PHP') === 0) {
            return false;
        }

        // Get the file from the map
        $file = $this->structureMap->getEntry($className);

        // Did we get something? If so we have to load it
        if ($file instanceof Structure) {
            require $file->getPath();
            return true;
        }

        // Still here? That sounds like bad news!
        return false;
    }

    /**
     * We will refill the cache dir by emptying it and filling it again
     *
     * @return bool
     */
    protected function refillCache()
    {

        // Lets clear the cache so we can fill it anew
        foreach (new \DirectoryIterator($this->getConfig()->getValue('cache/dir')) as $fileInfo) {

            if (!$fileInfo->isDot()) {

                // Unlink the file
                unlink($fileInfo->getPathname());
            }
        }

        // Lets create the definitions anew
        return $this->createDefinitions();
    }

    /**
     * Will register our autoloading method at the beginning of the spl autoloader stack
     *
     * @param boolean $throw   Should we throw an exception on error?
     * @param boolean $prepend If you want to NOT prepend you might, but you should not
     *
     * @return null
     */
    public function register($throw = true, $prepend = true)
    {

        // Now we have a config no matter what, we can store any instance we might need
        $this->getConfig()->storeInstances();

        // Query whether we have directories to omitted or not in production mode
        if ($this->autoloaderOmit || $this->environment !== 'production') { // If yes, load the apropriate autoloader method
            spl_autoload_register(array($this, self::OUR_LOADER), $throw, $prepend);
            return;
        }

        // We don't have directories to omit register the simple class loader
        spl_autoload_register(array($this, self::OUR_LOADER_PROD_NO_OMIT), $throw, $prepend);
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     *
     * @return void
     */
    public function unregister()
    {

        // Query whether we have directories to omitted or not in production mode
        if ($this->autoloaderOmit || $this->environment !== 'production') { // If yes, unload the apropriate autoloader method
            spl_autoload_unregister(array($this, self::OUR_LOADER));
            return;
        }

        // We don't have directories to omit unregister the simple class loader
        spl_autoload_unregister(array($this, self::OUR_LOADER_PROD_NO_OMIT), $throw, $prepend);
    }
}
