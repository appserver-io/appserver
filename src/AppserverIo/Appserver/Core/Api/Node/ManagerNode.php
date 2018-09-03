<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ManagerNode
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
use AppserverIo\Description\Api\Node\ParamsNodeTrait;

/**
 * DTO to transfer a manager.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ManagerNode extends AbstractNode implements ManagerNodeInterface
{

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Description\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * A security domains node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SecurityDomainsNodeTrait
     */
    use SecurityDomainsNodeTrait;

    /**
     * A authenticators node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthenticatorsNodeTrait
     */
    use AuthenticatorsNodeTrait;

    /**
     * A session handlers node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionHandlersNodeTrait
     */
    use SessionHandlersNodeTrait;

    /**
     * The object description configuration.
     *
     * @var array
     * @DI\Mapping(nodeName="objectDescription", nodeType="AppserverIo\Appserver\Core\Api\Node\ObjectDescriptionNode")
     */
    protected $objectDescription;

    /**
     * The unique manager name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The manager class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The managers factory class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * The context factory class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $contextFactory;

    /**
     * Initializes the manager configuration with the passed values.
     *
     * @param string                                                     $name              The unique manager name
     * @param string                                                     $type              The manager class name
     * @param string                                                     $factory           The managers factory class name
     * @param string                                                     $contextFactory    The context factory class name
     * @param \AppserverIo\Appserver\Core\Api\Node\ObjectDescriptionNode $objectDescription The object description configuration
     */
    public function __construct($name = '', $type = '', $factory = '', $contextFactory = '', $objectDescription = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->factory = $factory;
        $this->contextFactory = $contextFactory;
        $this->objectDescription = $objectDescription;
    }

    /**
     * Returns the application name.
     *
     * @return string The unique application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the class name.
     *
     * @return string The class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the factory class name.
     *
     * @return string The factory class name
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Returns the context factory class name.
     *
     * @return string The context factory class name
     */
    public function getContextFactory()
    {
        return $this->contextFactory;
    }

    /**
     * Returns the manager's object description configuration.
     *
     * @return array|\AppserverIo\Appserver\Core\Api\Node\ObjectDescriptionNode The object description configuration
     */
    public function getObjectDescription()
    {
        return $this->objectDescription;
    }

    /**
     * This method merges the configuration of the passed manager node
     * into this one.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerNode The node with the manager configuration we want to merge
     *
     * @return void
     */
    public function merge(ManagerNodeInterface $managerNode)
    {

        // make sure, we only merge nodes with the same name
        if (strcasecmp($this->getName(), $managerNode->getName()) !== 0) {
            return;
        }

        // override type and factory attributes
        $this->type = $managerNode->getType();
        $this->factory = $managerNode->getFactory();
        $this->contextFactory = $managerNode->getContextFactory();
        $this->objectDescription = $managerNode->getObjectDescription();

        // load the authenticators of this manager node
        $localAuthenticators = $this->getAuthenticators();

        // iterate over the authenticator nodes of the passed manager node and merge them
        foreach ($managerNode->getAuthenticators() as $authenticatorNode) {
            $isMerged = false;
            foreach ($localAuthenticators as $key => $localAuthenticator) {
                if (strcasecmp($localAuthenticator->getName(), $authenticatorNode->getName()) === 0) {
                    $localAuthenticators[$key] = $authenticatorNode;
                    $isMerged = true;
                }
            }
            if ($isMerged === false) {
                $localAuthenticators[$authenticatorNode->getUuid()] = $authenticatorNode;
            }
        }

        // override the authenticators with the merged mones
        $this->authenticators = $localAuthenticators;

        // override the params if available
        if (sizeof($params = $managerNode->getParams()) > 0) {
            $this->params = $params;
        }

        // override the security domains if available
        if (sizeof($securityDomains = $managerNode->getSecurityDomains()) > 0) {
            $this->securityDomains = $securityDomains;
        }
    }
}
