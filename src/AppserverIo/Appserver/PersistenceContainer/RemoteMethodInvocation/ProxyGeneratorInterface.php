<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation\ProxyGeneratorInterface
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

namespace AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation;

use AppserverIo\Psr\Deployment\DescriptorInterface;

/**
 * The interface for all proxy generator implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ProxyGeneratorInterface
{

    /**
     * Generate's a RMI proxy based on the passe descriptor information and
     * registers it in the naming directory.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $descriptor The descriptor with the proxy data used for generation
     *
     * @return void
     * @link https://github.com/appserver-io/rmi
     */
    public function generate(DescriptorInterface $descriptor);
}
