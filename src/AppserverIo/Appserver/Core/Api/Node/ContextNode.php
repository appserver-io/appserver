<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ContextNode
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
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;

/**
 * DTO to transfer server information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ContextNode extends AbstractNode
{

    /**
     * A class loader trait.
     *
     * @var \TraitInterface
     */
    use ClassLoadersNodeTrait;

    /**
     * A managers node trait.
     *
     * @var \TraitInterface
     */
    use ManagersNodeTrait;

    /**
     * A params node trait.
     *
     * @var \TraitInterface
     */
    use ParamsNodeTrait;

    /**
     * The context name,
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The context type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Initializes the context configuration with the passed values.
     *
     * @param string $name   The context name
     * @param string $type   The context class name
     * @param array  $params The context params
     */
    public function __construct($name = '', $type = '', array $params = array())
    {

        // set name and type
        $this->name = $name;
        $this->type = $type;
        $this->params = $params;

        // initialize the default directories
        $this->initDefaultDirectories();
    }

    /**
     * Initialize the default directories.
     *
     * @return void
     */
    public function initDefaultDirectories()
    {
        $this->setParam(DirectoryKeys::CACHE, ParamNode::TYPE_STRING, '/cache');
        $this->setParam(DirectoryKeys::SESSION, ParamNode::TYPE_STRING, '/session');
    }

    /**
     * Returns the context name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the context type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * This method merges the installation steps of the passed provisioning node into the steps of
     * this instance. If a installation node with the same type already exists, the one of this
     * instance will be overwritten.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ContextNode $contextNode The node with the installation steps we want to merge
     *
     * @return void
     */
    public function merge(ContextNode $contextNode)
    {

        // load the params defined in this context
        $localParams = $this->getParams();

        // merge them with the passed ones
        foreach ($contextNode->getParams() as $paramToMerge) {
            $isMerged = false;
            foreach ($localParams as $key => $param) {
                if ($param->getName() == $paramToMerge->getName()) {
                    $localParams[$key] = $paramToMerge;
                    $isMerged = true;
                }
            }
            if ($isMerged === false) {
                $localParams[$paramToMerge->getUuid()] = $paramToMerge;
            }
        }

        // set the params back to the context
        $this->setParams($localParams);

        // load the managers defined of this context
        $localManagers = $this->getManagers();

        // merge them with the passed ones
        foreach ($contextNode->getManagers() as $managerToMerge) {
            $isMerged = false;
            foreach ($localManagers as $key => $manager) {
                if ($manager->getName() === $managerToMerge->getName()) {
                    $localManagers[$key] = $managerToMerge;
                    $isMerged = true;
                }
            }
            if ($isMerged === false) {
                $localManagers[$managerToMerge->getUuid()] = $managerToMerge;
            }
        }

        // set the managers back to the context
        $this->setManagers($localManagers);

        // load the class loaders of this context
        $localClassLoaders = $this->getClassLoaders();

        // merge them with the passed ones
        foreach ($contextNode->getClassLoaders() as $classLoaderToMerge) {
            $isMerged = false;
            foreach ($localClassLoaders as $key => $classLoader) {
                if ($classLoader->getName() === $classLoaderToMerge->getName()) {
                    $localClassLoaders[$key] = $classLoaderToMerge;
                    $isMerged = true;
                }
            }
            if ($isMerged === false) {
                $localClassLoaders[$classLoaderToMerge->getUuid()] = $classLoaderToMerge;
            }
        }

        // set the class loaders back to the context
        $this->setClassLoaders($localClassLoaders);
    }
}
