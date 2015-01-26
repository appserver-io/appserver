<?php

/**
 * AppserverIo\Appserver\Core\Mock\MockSingletonSessionBean
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

/**
 * The mock singleton session bean implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @Singleton
 */
class MockSingletonSessionBean
{

    /**
     * A persistent value.
     *
     * @var string
     */
    protected $persistentValue;

    /**
     * Sets the persistent value.
     *
     * @param string $persistentValue The persistent value to set
     *
     * @retun void
     */
    public function setPersistentValue($persistentValue)
    {
        $this->persistentValue = $persistentValue;
    }

    /**
     * Returns the persistent value.
     *
     * @return string The persistent value
     */
    public function getPersistentValue()
    {
        return $this->persistentValue;
    }
}
