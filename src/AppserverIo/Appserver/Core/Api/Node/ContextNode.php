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

use AppserverIo\Description\Api\Node\AbstractNode;
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
     * @var \AppserverIo\Appserver\Core\Api\Node\ClassLoadersNodeTrait
     */
    use ClassLoadersNodeTrait;

    /**
     * The logger trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\LoggersNodeTrait
     */
    use LoggersNodeTrait;

    /**
     * A managers node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ManagersNodeTrait
     */
    use ManagersNodeTrait;

    /**
     * A managers node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ProvisionersNodeTrait
     */
    use ProvisionersNodeTrait;

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The context name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The application environment
     *
     * @var string
     */
    protected $environmentName;

    /**
     * The context type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The context factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * The path to the web application.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $webappPath;

    /**
     * Initializes the context configuration with the passed values.
     *
     * @param string $name       The context name
     * @param string $type       The context class name
     * @param string $factory    The context factory class name
     * @param array  $params     The context params
     * @param string $webappPath The path to the web application
     */
    public function __construct($name = '', $type = '', $factory = '', array $params = array(), $webappPath = '')
    {

        // set name, type and factory
        $this->name = $name;
        $this->type = $type;
        $this->factory = $factory;
        $this->params = $params;
        $this->webappPath = $webappPath;

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
     * Sets the context name.
     *
     * @param string $name The context name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Returns the environment name of the application
     *
     * @return string
     */
    public function getEnvironmentName()
    {
        return $this->environmentName;
    }

    /**
     * Setter for the environment name
     *
     * @param string $environmentName The environment name to set
     *
     * @return void
     */
    public function setEnvironmentName($environmentName)
    {
        $this->environmentName = $environmentName;
    }

    /**
     * Sets the context type.
     *
     * @param string $type The context type
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the context type.
     *
     * @return string|null The context type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the context factory class name.
     *
     * @param string $factory The context factory class name
     *
     * @return void
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;
    }

    /**
     * Returns the context factory class name.
     *
     * @return \AppserverIo\Appserver\Application\ApplicationFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Set's the path to the web application.
     *
     * @param string $webappPath The path to the web application
     *
     * @return void
     */
    public function setWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Returns the path to the web application.
     *
     * @return string
     */
    public function getWebappPath()
    {
        return $this->webappPath;
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

        // merge the application type
        if ($type = $contextNode->getType()) {
            $this->setType($type);
        }

        // merge the application factory class name
        if ($factory = $contextNode->getFactory()) {
            $this->setFactory($factory);
        }

        // merge the application webapp path
        if ($webappPath = $contextNode->getWebappPath()) {
            $this->setWebappPath($webappPath);
        }

        // load the params defined in this context
        $localParams = $this->getParams();

        // merge them with the passed ones
        foreach ($contextNode->getParams() as $paramToMerge) {
            $isMerged = false;
            /** @var \AppserverIo\Appserver\Core\Api\Node\ParamNode $param */
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
        /**  @var \AppserverIo\Appserver\Core\Api\Node\ManagerNode $managerToMerge */
        foreach ($contextNode->getManagers() as $managerToMerge) {
            $isMerged = false;
            /** @var \AppserverIo\Appserver\Core\Api\Node\ManagerNode $manager */
            foreach ($localManagers as $key => $manager) {
                if ($manager->getName() === $managerToMerge->getName()) {
                    $manager->merge($managerToMerge);
                    $localManagers[$key] = $manager;
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
        /** @var \AppserverIo\Appserver\Core\Api\Node\ClassLoaderNode $classLoaderToMerge */
        foreach ($contextNode->getClassLoaders() as $classLoaderToMerge) {
            $isMerged = false;
            /** @var \AppserverIo\Appserver\Core\Api\Node\ClassLoaderNode $classLoader */
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

        // load the loggers of this context
        $localLoggers = $this->getLoggers();

        // merge them with the passed ones (DO override already registered loggers)
        /** @var \AppserverIo\Appserver\Core\Api\Node\LoggerNode $loggerToMerge */
        foreach ($contextNode->getLoggers() as $loggerToMerge) {
            $localLoggers[$loggerToMerge->getName()] = $loggerToMerge;
        }

        // set the loggers back to the context
        $this->setLoggers($localLoggers);
    }
}
