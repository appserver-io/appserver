<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\SecurityDomainInterface
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

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginModuleInterface;

/**
 * Interface for a security domain implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SecurityDomainInterface
{

    /**
     * Return's the name of the security domain.
     *
     * @return string The security domain's name
     */
    public function getName();

    /**
     * Add's the passed login module to the security domain.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginModuleInterface $loginModule The login module to add
     *
     * @return void
     */
    public function addLoginModule(LoginModuleInterface $loginModule);
}
