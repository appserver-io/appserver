<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\EpbNode
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

/**
 * DTO to transfer enterprise beans information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EpbNode extends AbstractNode implements EpbNodeInterface
{

    /**
     * The enterprise beans information.
     *
     * @var array
     * @AS\Mapping(nodeName="enterprise-beans", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\EnterpriseBeansNode")
     */
    protected $enterpriseBeans;

    /**
     * Return's the enterprise beans information.
     *
     * @return array The enterprise beans information
     */
    public function getEnterpriseBeans()
    {
        return $this->enterpriseBeans;
    }
}
