<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNode
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
 * DTO to transfer a security domain configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SecurityDomainNode extends AbstractNode implements SecurityDomainNodeInterface
{

    /**
     * Trait to handle the authorization configuration.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthConfigsNodeTrait
     */
    use AuthConfigsNodeTrait;

    /**
     * The security domain name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * Return's the security domain name.
     *
     * @return string The security domain name
     */
    public function getName()
    {
        return $this->name;
    }
}
