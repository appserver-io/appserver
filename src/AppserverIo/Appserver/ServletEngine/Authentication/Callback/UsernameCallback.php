<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\Callback\UsernameCallback
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

namespace AppserverIo\Appserver\ServletEngine\Authentication\Callback;

use AppserverIo\Collections\CollectionInterface;

/**
 * An abstract login module implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class UsernameCallback implements CallbackInterface
{

    const DEFAULT_NAME = 'guest';

    protected $username;

    protected $defaultName;

    public function __construct($defaultName = UsernameCallback::DEFAULT_NAME)
    {
        $this->setDefaultName($defaultName);
    }

    public function setDefaultName($defaultName)
    {
        $this->defaultName = $defaultName;
    }

    public function getDefaultName()
    {
        return $this->defaultName;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }
}
