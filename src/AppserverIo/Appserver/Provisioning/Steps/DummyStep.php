<?php

/**
 * AppserverIo\Appserver\Provisioning\Steps\DummyStep
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Provisioning\Steps;

/**
 * A dummy step implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DummyStep extends AbstractStep
{

    /**
     * Executes the functionality for this step, nothing in this case,
     * because this is a dummy implementation.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\Provisioning\StepInterface::execute()
     */
    public function execute()
    {
        // do nothing here, we're a dummy
    }
}
