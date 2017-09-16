<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\DiNode
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

use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer DI information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DiNode extends AbstractNode implements DiNodeInterface
{

    /**
     * The beans information.
     *
     * @var array
     * @AS\Mapping(nodeName="beans/bean", nodeType="array", elementType="AppserverIo\Description\Api\Node\BeanNode")
     */
    protected $beans;

    /**
     * The beans information.
     *
     * @var array
     * @AS\Mapping(nodeName="preferences/preference", nodeType="array", elementType="AppserverIo\Description\Api\Node\PreferenceNode")
     */
    protected $preferences;

    /**
     * Return's the bean informations.
     *
     * @return array The enterprise bean informations
     */
    public function getBeans()
    {
        return $this->beans;
    }

    /**
     * Return's the preference informations.
     *
     * @return array The preference informations
     */
    public function getPreferences()
    {
        return $this->preferences;
    }
}
