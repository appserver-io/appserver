<?php
/**
 * \AppserverIo\Appserver\Core\GeneratorThread
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
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Doppelgaenger\Generator;

/**
 * Simple thread for parallel creation of contract-enabled structure definitions.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
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

        // register the default autoloader
        require SERVER_AUTOLOADER;

        // iterate over all structures and generate them
        foreach ($this->structures as $structure) {
            $this->generator->create($structure);
        }
    }
}
