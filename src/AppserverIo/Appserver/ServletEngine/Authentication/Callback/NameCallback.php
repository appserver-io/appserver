<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\Callback\NameCallback
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

    /**
     * The login name.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $name;

    /**
     * The default login name to use.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $defaultName;

    /**
     * Initialize the username callback.
     */
    public function __construct()
    {
        $this->setDefaultName(new String(NameCallback::DEFAULT_NAME));
    }

    /**
     * Set's the default login name.
     *
     * @param \AppserverIo\Lang\String $defaultName The default name
     *
     * @return void
     */
    public function setDefaultName(String $defaultName)
    {
        $this->defaultName = $defaultName;
    }

    /**
     * Return's the default name.
     *
     * @return \AppserverIo\Lang\String The default name
     */
    public function getDefaultName()
    {
        return $this->defaultName;
    }

    /**
     * Set's the login name.
     *
     * @param \AppserverIo\Lang\String $name The login name to set
     *
     * @return void
     */
    public function setName(String $name)
    {
        $this->name = $name;
    }

    /**
     * Return's the login name.
     *
     * @return \AppserverIo\Lang\String The login name
     */
    public function getName()
    {
        return $this->name;
    }
}
