<?php
/**
 * TechDivision\ApplicationServer\DbcClassLoader
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

use TechDivision\PBC\CacheMap;
use TechDivision\PBC\Generator;
use TechDivision\PBC\StructureMap;
use TechDivision\PBC\Config;
use TechDivision\PBC\AutoLoader;
use TechDivision\ApplicationServer\InitialContext;

// We should get the composer autoloader as a fallback
require '/opt/appserver/app/code/vendor/autoload.php';

/**
 * This class is used to delegate to php-by-contract's autoloader.
 * This is needed as our multi-threaded environment would not allow any out-of-the-box code generation
 * in an on-the-fly manner.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class PbcClassLoader extends SplClassLoader
{

    /**
     * @var \TechDivision\PBC\AutoLoader  The original PBC Autoloader we will delegate to
     */
    protected $autoloader;

    /**
     * @var \TechDivision\PBC\Config  Will hold the PBC configuration
     */
    protected $config;

    /**
     * @var \TechDivision\ApplicationServer\InitialContext  Will hold the initial context instance
     */
    protected $initialContext;

    protected $structureMap;

    protected $map;

    /**
     * @const string    The name of our autoload method
     */
    const OUR_LOADER = 'loadClass';

    /**
     * @const string    Our default configuration file
     */
    const CONFIG_FILE = '/opt/appserver/etc/pbc.conf.json';

    /**
     * Default constructor
     *
     * We will do all the necessary work to load without any further hassle.
     * Will check if there is content in the cache directory.
     * If not we will parse anew.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext Will give us the needed initial context
     */
    public function __construct($initialContext)
    {
        $this->initialContext = $initialContext;

        // Get our Config instance and load our configuration
        $this->config = Config::getInstance();
        $this->config = $this->config->load(self::CONFIG_FILE);

        // We need the cacheing configuration
        $cacheConfig = $this->config->getConfig('cache');

        // We need a standalone structure map to check for its version
        $this->structureMap = new StructureMapWrapper($this->config->getConfig(
            'autoloader'
        )['dirs'], $this->config->getConfig('enforcement')['dirs'], $this->config, $initialContext);

        if (empty($this->autoloader)) {

            // We need our autoloader to delegate to.
            $this->autoloader = new AutoLoader($this->structureMap);
        }

        // Check if there are files in the cache.
        // If not we will fill the cache, if there are we will check if there have been any changes to the project dirs
        $fileIterator = new \FilesystemIterator($cacheConfig['dir'], \FilesystemIterator::SKIP_DOTS);
        if (iterator_count($fileIterator) <= 1 || $this->config->getConfig('environment') === 'development') {

            // Fill the cache
            $this->fillCache();

        } else {

            if (!$this->structureMap->isRecent()) {

                $this->refillCache($cacheConfig);

            }
        }
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
     * We will refill the cache dir by emptying it and filling it again
     *
     * @param array $cacheConfig The cache config as array
     *
     * @return bool
     */
    protected function refillCache(array $cacheConfig)
    {
        // Lets clear the cache so we can fill it anew
        foreach (new \DirectoryIterator($cacheConfig['dir']) as $fileInfo) {

            if (!$fileInfo->isDot()) {

                // Unlink the file
                unlink($fileInfo->getPathname());
            }
        }

        // Lets create the definitions anew
        return $this->createDefinitions($this->structureMap);
    }

    /**
     * Creates the definitions by given structure map
     *
     * @return bool
     */
    protected function createDefinitions()
    {
        // Get all the structures we found
        $structures = $this->structureMap->getEntries(true);

        // We will need a CacheMap instance which we can pass to the generator
        // We need the caching configuration
        $cacheConfig = $this->config->getConfig('cache');
        $cacheMap = new CacheMap($cacheConfig['dir'], array());

        // We need a generator so we can create our proxies initially
        $generator = new Generator($this->structureMap, $cacheMap);

        // Iterate over all found structures and generate their proxies, but ignore the ones with omitted
        // namespaces
        $autoLoaderConfig = $this->config->getConfig('autoloader');
        $omittedNamespaces = array();
        if (isset($autoLoaderConfig['omit'])) {

            $omittedNamespaces = $autoLoaderConfig['omit'];
        }
        foreach ($structures as $structure) {

            // Working on our own files has very weird side effects, so don't do it
            if (strpos($structure->getIdentifier(), 'TechDivision\PBC') !== false || !$structure->isEnforced()) {

                continue;
            }

            // Might this be an omitted structure after all?
            foreach ($omittedNamespaces as $omittedNamespace) {

                // If our class name begins with the omitted part e.g. it's namespace
                if (strpos($structure->getIdentifier(), $omittedNamespace) === 0) {

                    continue 2;
                }
            }

            // Create the new file
            $generator->create($structure);
        }

        // Still here? Sounds about right
        return true;
    }

    /**
     * Our class loading method.
     *
     * This method will delegate to the php-by-contract's AutoLoader class.
     *
     * @param string $className Name of the structure to load
     *
     * @return  bool
     */
    public function loadClass($className)
    {
        // Try our loader first
        $tmp = $this->autoloader->loadClass($className);

        // Did we succeed?
        if ($tmp === true) {

            return $tmp;

        } else {

            // Call the parent constructor so we can build up the environment
            parent::__construct($this->initialContext);

            // Delegate to the parent class loader
            return parent::loadClass($className);
        }
    }

    /**
     * Will register this autoloader as first one on the stack.
     * We already register the composer loader as a fallback.
     *
     * @param bool $throws   SplAutoload compatible
     * @param bool $prepends SplAutoload compatible but will be ignored
     *
     * @return void
     */
    public function register($throws = true, $prepends = true)
    {
        // We want to let our autoloader be the first in line so we can react on loads and create/return our proxies.
        // So lets use the prepend parameter here.
        spl_autoload_register(array($this, self::OUR_LOADER), $throws, true);
    }
}
