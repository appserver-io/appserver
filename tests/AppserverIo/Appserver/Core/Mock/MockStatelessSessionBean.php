<?php

/**
 * AppserverIo\Appserver\Core\Mock\MockStatelessSessionBean
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
 * The mock stateless session bean implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @Stateless
 */
class MockStatelessSessionBean
{

    /**
     * Some value for testing purposes.
     *
     * @var string
     */
    protected $aValue;

    /**
     * Sets a value for testing purposes.
     *
     * @param string $aValue A value for testing purposes
     */
    public function setAValue($aValue)
    {
        $this->aValue = $aValue;
    }

    /**
     * Returns the value for testing purposes.
     *
     * @return string The value for testing purposes
     */
    public function getAValue()
    {
        return $this->aValue;
    }
}
