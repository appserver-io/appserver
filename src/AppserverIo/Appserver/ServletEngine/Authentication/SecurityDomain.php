<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\SecurityDomain
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

use AppserverIo\Collections\ArrayList;
use AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginModuleInterface;

/**
 * Security domain implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SecurityDomain
{

    /**
     * The security domain's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The security domain's login modules.
     *
     * @var \AppserverIo\Collections\ArrayList
     */
    protected $loginModules;

    /**
     * Initialize the security domain with the passed name.
     *
     * @param string $name The security domain's name
     */
    public function __construct($name)
    {

        // set the passed name
        $this->name = $name;

        // initialize the array list for the login modules
        $this->loginModules = new ArrayList();
    }

    /**
     * Return's the name of the security domain.
     *
     * @return string The security domain's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add's the passed login module to the security domain.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginModuleInterface $loginModule The login module to add
     *
     * @return void
     */
    public function addLoginModule(LoginModuleInterface $loginModule)
    {
        $this->loginModules->add($loginModule);
    }
}
