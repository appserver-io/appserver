<?php

/**
 * TechDivision\ApplicationServer\Provisioning\DummyStep
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Provisioning;

/**
 * A dummy step implementation.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DummyStep extends AbstractStep
{
    
    /**
     * Executes the functionality for this step, nothing in this case,
     * because this is a dummy implementation.
     * 
     * @return void
     * @see \TechDivision\ApplicationServer\Provisioning\Step::execute()
     */
    public function execute()
    {
        // do nothing here, we're a dummy
    }
}
