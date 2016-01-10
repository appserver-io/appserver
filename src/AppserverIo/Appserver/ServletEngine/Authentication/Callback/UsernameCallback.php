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

use AppserverIo\Lang\String;
/**
 * An abstract login module implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class NameCallback implements CallbackInterface
{

    /**
     * The default username.
     *
     * @var string
     */
    const DEFAULT_NAME = 'guest';

    protected $name;

    protected $defaultName;

    /**
     * Initialize the username callback.
     */
    public function __construct()
    {
        $this->setDefaultName(new String(NameCallback::DEFAULT_NAME));
    }

    public function setDefaultName($defaultName)
    {
        $this->defaultName = $defaultName;
    }

    public function getDefaultName()
    {
        return $this->defaultName;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
