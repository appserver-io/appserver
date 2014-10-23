<?php
/**
 * TechDivision\ApplicationServer\PbcAutoLoader
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

use TechDivision\ApplicationServer\Interfaces\ClassLoaderInterface;
use TechDivision\PBC\CacheMap;
use TechDivision\PBC\Generator;
use TechDivision\PBC\StructureMap;
use TechDivision\PBC\Config;
use TechDivision\PBC\AutoLoader;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface;

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
class PbcClassLoader extends AutoLoader implements ClassLoaderInterface
{

    /**
     * The configuration we base our actions on.
     *
     * @var \TechDivision\PBC\Config
     */
    public $config;

    /**
     * Cache map to keep track of already processed files.
     *
     * @var \TechDivision\PBC\CacheMap
     */
    public $cache;

    /**
     * In some cases the autoloader instance is not thrown away, saving the structure map might be a benefit here.
     *
     * @var \TechDivision\PBC\StructureMap $structureMap
     */
    public $structureMap;

    /**
     * Our default configuration file
     *
     * @const string CONFIG_FILE
     */
    const CONFIG_FILE = '/opt/appserver/etc/pbc.conf.json';

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
    const IDENTIFIER = 'pbc';

    /**
     * The name of our autoload method
     *
     * @const string OUR_LOADER
     */
    const OUR_LOADER = 'loadClass';

    /**
     * Default constructor
     *
     * We will do all the necessary work to load without any further hassle.
     * Will check if there is content in the cache directory.
     * If not we will parse anew.
     *
     * @param \TechDivision\PBC\Config|null $config An already existing config instance
     */
    public function __construct(Config $config = null)
    {
        // If we got a config we can use it, if not we will get a context less config instance
        if (is_null($config)) {

            $config = new Config();
            $config->load(self::CONFIG_FILE);
        }

        // Construct the parent from the config we got
        parent::__construct($config);

        // We now have a structure map instance, so fill it initially
        $this->getStructureMap()->fill();

        // Check if there are files in the cache.
        // If not we will fill the cache, if there are we will check if there have been any changes to the enforced dirs
        $fileIterator = new \FilesystemIterator($this->config->getValue('cache/dir'), \FilesystemIterator::SKIP_DOTS);
        if (iterator_count($fileIterator) <= 1 || $this->config->getValue('environment') === 'development') {

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
        $cacheMap = new CacheMap($this->config->getValue('cache/dir'), array(), $this->config);

        // We need a generator so we can create our proxies initially
        $generator = new Generator($this->structureMap, $cacheMap, $this->config);

        // Iterate over all found structures and generate their proxies, but ignore the ones with omitted
        // namespaces
        $omittedNamespaces = array();
        if ($this->config->hasValue('autoloader/omit')) {

            $omittedNamespaces = $this->config->getValue('autoloader/omit');
        }

        // Now check which structures we have to create and split them up for multi-threaded creation
        $generatorStack = array();
        foreach ($structures as $identifier => $structure) {

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
     * We will refill the cache dir by emptying it and filling it again
     *
     * @return bool
     */
    protected function refillCache()
    {
        // Lets clear the cache so we can fill it anew
        foreach (new \DirectoryIterator($this->config->getValue('cache/dir')) as $fileInfo) {

            if (!$fileInfo->isDot()) {

                // Unlink the file
                unlink($fileInfo->getPathname());
            }
        }

        // Lets create the definitions anew
        return $this->createDefinitions();
    }
}
