<?php

/**
 * AppserverIo\Appserver\Core\Mock\MockDeployment
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
namespace AppserverIo\Appserver\Core\Mock;

use AppserverIo\Appserver\Core\AbstractDeployment;

/**
 * A mock deployment implementation.
 *
 * @author    Thomas Kreidenhuber <t.kreidenhuber@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MockDeployment extends AbstractDeployment
{

    /**
     * Initializes the available applications and adds them to the deployment instance.
     *
     * @return void
     */
    public function deploy()
    {
        return $this;
    }
}