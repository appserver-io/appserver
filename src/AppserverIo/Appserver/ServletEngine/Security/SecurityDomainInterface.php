<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\SecurityDomainInterface
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

namespace AppserverIo\Appserver\ServletEngine\Security;

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
     * Return's the security domain's configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface The security domain's configuration
     */
    public function getConfiguration();
}
