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

use TechDivision\PBC\CacheMap;
use TechDivision\PBC\Generator;
use TechDivision\PBC\StructureMap;
use TechDivision\PBC\Config;
use TechDivision\PBC\AutoLoader;

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
     * @var AutoLoader
     */
    protected $autoloader;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var InitialContext
     */
    protected $initialContext;

    /**
     * @const string
     */
    const OUR_LOADER = 'loadClass';

    /**
     * @const string
     */
    const CONFIG_FILE = '/opt/appserver/etc/pbc.conf.json';

    /**
     * Default constructor
     *
     * We will do all the necessary work to load without any further hassle.
     * Will check if there is content in the cache directory.
     * If not we will parse anew.
     */
    public function __construct($initialContext)
    {
        $this->initialContext = $initialContext;

        // Get our Config instance and load our configuration
        $this->config = Config::getInstance();
        $this->config = $this->config->load(self::CONFIG_FILE);

        // We need the cacheing configuration
        $cacheConfig = $this->config->getConfig('cache');

        // We need our autoloader to delegate to
        $this->autoloader = new AutoLoader();

        // Check if there are files in the cache
        $fileIterator = new \FilesystemIterator($cacheConfig['dir'], \FilesystemIterator::SKIP_DOTS);
        if (iterator_count($fileIterator) <= 2 || $this->config->getConfig('environment') === 'development') {

            $this->fillCache();
        }
    }

    /**
     * Will initiate the creation of a structure map and the parsing process of all found structures
     */
    protected function fillCache()
    {
        // We will need the structure map to initially parse all files
        $structureMap = new StructureMap($this->config->getConfig('project-dirs'), $this->config);

        // Get all the structures we found
        $structures = $structureMap->getEntries(true);

        // We will need a CacheMap instance which we can pass to the generator
        // We need the cacheing configuration
        $cacheConfig = $this->config->getConfig('cache');
        $cacheMap = new CacheMap($cacheConfig['dir']);

        // We need a generator so we can create our proxies initially
        $generator = new Generator($cacheMap);

        // Iterate over all found structures and generate their proxies, but ignore the ones with omitted
        // namespaces
        $autoLoaderConfig = $this->config->getConfig('autoloader');
        $omittedNamespaces = array();
        if (isset($autoLoaderConfig['omit'])) {

            $omittedNamespaces = $autoLoaderConfig['omit'];
        }
        foreach ($structures as $structure) {

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
        $tmp = $this->autoloader->loadClass($className);

        if ($tmp === true) {

            return $tmp;

        } else {

            return parent::loadClass($className);
        }
    }

    /**
     * @param bool $throws
     * @param bool $prepends
     */
    public function register($throws = true, $prepends = true)
    {
        // We should get the composer autoloader as a fallback
        require '/opt/appserver/app/code/vendor/autoload.php';

        // We want to let our autoloader be the first in line so we can react on loads and create/return our proxies.
        // So lets use the prepend parameter here.
        spl_autoload_register(array($this, self::OUR_LOADER), $throws, true);
    }
}