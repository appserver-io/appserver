<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\RealmFactoryInterface
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
 * @copyright 2021 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Psr\Auth\AuthenticationManagerInterface;
use AppserverIo\Psr\Security\Auth\Login\SecurityDomainConfigurationInterface;

/**
 * Realm factory interface.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2021 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface RealmFactoryInterface
{

    /**
     * Initialize the security domain with the passed name.
     *
     * @param \AppserverIo\Psr\Auth\AuthenticationManagerInterface                      $authenticationManager The authentication manager instance
     * @param \AppserverIo\Psr\Security\Auth\Login\SecurityDomainConfigurationInterface $configuration         The realm's configuration
     *
     * @return \AppserverIo\Psr\Auth\RealmInterface The realm instance
     */
    public function create(AuthenticationManagerInterface $authenticationManager, SecurityDomainConfigurationInterface $configuration);
}
