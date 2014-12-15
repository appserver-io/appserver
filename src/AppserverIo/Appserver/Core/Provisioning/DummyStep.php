<?php

/**
 * AppserverIo\Appserver\Core\Provisioning\DummyStep
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Provisioning;

/**
 * A dummy step implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
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
     * @see \AppserverIo\Appserver\Core\Provisioning\Step::execute()
     */
    public function execute()
    {
        // do nothing here, we're a dummy
    }
}
