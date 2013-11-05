<?php
/**
 * TechDivision\ApplicationServer\DbcClassLoader
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\PBC\AutoLoader;
use TechDivision\PBC\CacheMap;
use TechDivision\PBC\Proxies\ProxyFactory;
use TechDivision\PBC\StructureMap;
use TechDivision\PBC\Config;

/**
 * This class is used to delegate to php-by-contract's autoloader.
 * This is needed as our multi-threaded environment would not allow any out-of-the-box code generation
 * in an on-the-fly manner.
 *
 * @package    TechDivision\ApplicationServer
 * @copyright  Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @author     Bernhard Wick <b.wick@techdivision.com>
 */
class DbcClassLoader extends SplClassLoader
{

    /**
     * @var TechDivision\PBC\AutoLoader
     */
    private $autoLoader;

    /**
     * @const string
     */
    const OUR_LOADER = 'loadClass';

    /**
     * Default constructor
     *
     * We will do all the necessary work to load without any further hassle.
     * Will check if there is content in the cache directory.
     * If not we will parse anew.
     */
    public function __construct()
    {
        // We need an autoloader to delegate to
        $this->autoLoader = new AutoLoader();

        // Check if there are files in the cache
        $fileIterator = new \FilesystemIterator(PBC_CACHE_DIR, \FilesystemIterator::SKIP_DOTS);
        if (iterator_count($fileIterator) <= 2) {

            $this->fillCache();
        }
    }

    /**
     * Will initiate the creation of a structure map and the parsing process of all found structures
     */
    protected function fillCache()
    {
        // Get the configuration, get the AutoLoader specific config as well
        $config = new Config();
        $autoLoaderConfig = $config->getConfig('AutoLoader');

        // We will need the structure map to initially parse all files
        $structureMap = new StructureMap($autoLoaderConfig['projectRoot'], $this->config);

        // Get all the structures we found
        $structures = $structureMap->getEntries();

        // We will need a CacheMap instance which we can pass to the ProxyFactory
        $cacheMap = new CacheMap(PBC_CACHE_DIR);

        // We need a ProxyFactory so we can create our proxies initially
        $proxyFactory = new ProxyFactory($structureMap, $cacheMap);

        // Iterate over all found structures and generate their proxies
        foreach ($structures as $structure) {

            if ($structure->hasContracts()) {

                $proxyFactory->createProxy($structure->getIdentifier());
            }
        }
    }

    /**
     * Our class loading method.
     *
     * This method will delegate to the php-by-contract's AutoLoader class.
     *
     * @param   string $className
     *
     * @return  bool
     */
    public function loadClass($className)
    {
        // Do we have the file in our cache dir? If we are in development mode we have to ignore this.
        $cachePath = PBC_CACHE_DIR . DIRECTORY_SEPARATOR . str_replace('\\', '_', $className) . '.php';
        if (is_readable($cachePath)) {

            require $cachePath;
            return true;
        }

        return false;
    }

    /**
     * @param bool $throws
     * @param bool $prepends
     */
    public function register($throws = true, $prepends = true)
    {
        // We will unregister all currently registered classloader, as multi-threading messes the list up badly!
        $functions = spl_autoload_functions();
        if (is_array($functions)) {

            foreach ($functions as $function) {

                spl_autoload_unregister($function);
            }
        }

        // Register the default loader, so we can load files which do not have any contracts
        $classLoader = new SplClassLoader();
        $classLoader->register($throws, $prepends);

        // We want to let our autoloader be the first in line so we can react on loads and create/return our proxies.
        // So lets use the prepend parameter here.
        spl_autoload_register(array($this, self::OUR_LOADER), $throws, true);
    }
}