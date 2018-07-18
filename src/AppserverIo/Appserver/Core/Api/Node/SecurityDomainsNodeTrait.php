<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SecurityDomainsNodeTrait
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;

/**
 * Trait to handle security domain nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait SecurityDomainsNodeTrait
{

    /**
     * The security domain configuration.
     *
     * @var array
     * @DI\Mapping(nodeName="securityDomains/securityDomain", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\SecurityDomainNode")
     */
    protected $securityDomains = array();

    /**
     * Sets the security domain configuration.
     *
     * @param array $securityDomains The security domain configuration
     *
     * @return void
     */
    public function setSecurityDomains($securityDomains)
    {
        $this->securityDomains = $securityDomains;
    }

    /**
     * Returns the security domain configuration.
     *
     * @return array The security domain configuration
     */
    public function getSecurityDomains()
    {
        return $this->securityDomains;
    }
}
