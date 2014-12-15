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
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Doppelgaenger\Generator;

/**
 * AppserverIo\Appserver\Core\GeneratorThread
 *
 * Simple thread for parallel creation of contract-enabled structure definitions
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class GeneratorThread extends \Thread
{
    /**
     * Generator instance to use for creation
     *
     * @var \AppserverIo\Doppelgaenger\Generator $generator
     */
    protected $generator;

    /**
     * Array of structures we will be creating
     *
     * @var array<\AppserverIo\Doppelgaenger\Entities\Definitions\Structure> $structures
     */
    protected $structures;

    /**
     * Default constructor
     *
     * @param \AppserverIo\Doppelgaenger\Generator $generator  Our Doppelgaenger generator instance
     * @param array                                $structures List of structures to generate
     */
    public function __construct(Generator $generator, array $structures)
    {
        $this->generator = $generator;
        $this->structures = $structures;
    }

    /**
     * Run method
     *
     * @return void
     */
    public function run()
    {

        // require the composer autoloader
        require APPSERVER_BP . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

        // iterate over all structures and generate them
        foreach ($this->structures as $structure) {
            $this->generator->create($structure);
        }
    }
}
