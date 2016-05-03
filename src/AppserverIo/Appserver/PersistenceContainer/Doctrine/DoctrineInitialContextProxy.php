<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Doctrine\DoctrineInitialContextProxy
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

namespace AppserverIo\Appserver\PersistenceContainer\Doctrine;

use AppserverIo\RemoteMethodInvocation\RemoteProxy;

/**
 * Proxy for the container instance itself.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DoctrineInitialContextProxy extends RemoteProxy
{

    /**
     * Runs a lookup on the container for the class with the
     * passed name.
     *
     * @param string $className The class name to run the lookup for
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The instance
     */
    public function lookup($className)
    {
        return DoctrineEntityManagerProxy::__create($className)->__setSession($this->__getSession());
    }
}
