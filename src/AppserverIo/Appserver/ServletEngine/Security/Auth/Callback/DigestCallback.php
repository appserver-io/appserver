<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Callback\SecurityAssociationHandler
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

namespace AppserverIo\Appserver\ServletEngine\Security\Auth\Callback;

use AppserverIo\Collections\HashMap;

/**
 * A digest callback implementation that allows to customize the digest processing.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DigestCallback
{

    /**
     * Initializes the callback with the configuration params.
     *
     * @param \AppserverIo\Collections\HashMap $params The configuration params
     *
     * @return void
     */
    public function init(HashMap $params)
    {
        $this->params = $params;
    }

    /**
     * @TODO
     *
     * @param object $messageDigest The message digest to handle
     *
     * @return void
     */
    public function preDigest($messageDigest)
    {
    }

    /**
     * @TODO
     *
     * @param object $messageDigest The message digest to handle
     *
     * @return void
     */
    public function postDigest($messageDigest)
    {
    }
}
