<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\FormLoginConfigNodeInterface
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for a login form configuration DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface FormLoginConfigNodeInterface extends NodeInterface
{

    /**
     * The default login error handler callback class name.
     *
     * @var string
     */
    const DEFAULT_LOGIN_CALLBACK = 'AppserverIo\Appserver\ServletEngine\Authenticator\Callback\LoginErrorCallback';

    /**
     * Return's the form login page information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FormLoginPageNode The form login page information
     */
    public function getFormLoginPage();

    /**
     * Return's the form login callback information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FormLoginCallbackNode The form login callback information
     */
    public function getFormLoginCallback();

    /**
     * Return's the form error page information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FormErrorPageNode The form error page information
     */
    public function getFormErrorPage();
}
