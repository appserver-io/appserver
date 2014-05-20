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
 * @category   Appserver
 * @package    TechDivision
 * @subpackage ApplicationServer
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer;

use TechDivision\PBC\Generator;

/**
 * TechDivision\ApplicationServer\GeneratorThread
 *
 * <TODO CLASS DESCRIPTION>
 *
 * @category   Appserver
 * @package    TechDivision
 * @subpackage ApplicationServer
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class GeneratorThread extends \Thread
{
    /**
     * @var  $generator <TODO FIELD COMMENT>
     */
    protected $generator;

    /**
     * @var  $structures <TODO FIELD COMMENT>
     */
    protected $structures;

    /**
     * Default constructor
     *
     * @param \TechDivision\PBC\Generator                    $generator      Our PBC generator instance
     * @param array                                          $structures     List of structures to generate
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext Will give us the needed initial context
     */
    public function __construct(Generator $generator, array $structures, $initialContext)
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
        require '/opt/appserver/app/code/vendor/autoload.php';

        // Iterate over all structures and generate them
        foreach ($this->structures as $structure) {

            $this->generator->create($structure);
        }
    }
}
