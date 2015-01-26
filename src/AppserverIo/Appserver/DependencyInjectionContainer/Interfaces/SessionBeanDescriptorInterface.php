<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\SessionBeanDescriptorInterface
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

namespace AppserverIo\Appserver\DependencyInjectionContainer\Interfaces;

/**
 * Interface for a bean descriptor.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SessionBeanDescriptorInterface extends BeanDescriptorInterface
{

    /**
     * Returns the beans session type.
     *
     * @return string The beans session type
     */
    public function getSessionType();

    /**
     * Returns the local interface name.
     *
     * @return string The local interface name
     */
    public function getLocal();

    /**
     * Returns the remote interface name.
     *
     * @return string The remote interface name
     */
    public function getRemote();

    /**
     * The array with the post construct callback method names.
     *
     * @return array The post construct callback method names
     */
    public function getPostConstructCallbacks();

    /**
     * The array with the pre destroy callback method names.
     *
     * @return array The pre destroy callback method names
     */
    public function getPreDestroyCallbacks();
}
