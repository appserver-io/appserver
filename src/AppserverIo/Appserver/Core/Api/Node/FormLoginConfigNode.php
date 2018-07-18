<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\FormLoginConfigNode
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

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer a login form configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FormLoginConfigNode extends AbstractNode implements FormLoginConfigNodeInterface
{

    /**
     * The form login page information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\FormLoginPageNode
     * @DI\Mapping(nodeName="form-login-page", nodeType="AppserverIo\Appserver\Core\Api\Node\FormLoginPageNode")
     */
    protected $formLoginPage;

    /**
     * The form error page information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\FormErrorPageNode
     * @DI\Mapping(nodeName="form-error-page", nodeType="AppserverIo\Appserver\Core\Api\Node\FormErrorPageNode")
     */
    protected $formErrorPage;

    /**
     * Return's the form login page information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FormLoginPageNode The form login page information
     */
    public function getFormLoginPage()
    {
        return $this->formLoginPage;
    }

    /**
     * Return's the form error page information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FormErrorPageNode The form error page information
     */
    public function getFormErrorPage()
    {
        return $this->formErrorPage;
    }
}
