<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\HostNode
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
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Psr\ApplicationServer\Configuration\HostConfigurationInterface;

/**
 * DTO to transfer a host.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class HostNode extends AbstractNode implements HostConfigurationInterface
{

    /**
     * A context node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ContextsNodeTrait
     */
    use ContextsNodeTrait;

    /**
     * The host name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The application base directory.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $appBase;

    /**
     * The temporary base directory.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $tmpBase;

    /**
     * The deployment base directory.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $deployBase;

    /**
     * Initializes the node with the passed data.
     *
     * @param string $name       The host name
     * @param string $appBase    The application base directory
     * @param string $tmpBase    The temporary base directory
     * @param string $deployBase The deployment base directory
     */
    public function __construct($name = '', $appBase = 'webapps', $tmpBase = 'var/tmp', $deployBase = 'deploy')
    {
        $this->name = $name;
        $this->appBase = $appBase;
        $this->tmpBase = $tmpBase;
        $this->deployBase = $deployBase;
    }

    /**
     * Returns the host name.
     *
     * @return string The host name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the application base directory.
     *
     * @return string The application base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * Returns the temporary base directory.
     *
     * @return string The temporary base directory
     */
    public function getTmpBase()
    {
        return $this->tmpBase;
    }

    /**
     * Returns the deployment base directory.
     *
     * @return string The deployment base directory
     */
    public function getDeployBase()
    {
        return $this->deployBase;
    }

    /**
     * Return's the host's directories, e. g. to be created.
     *
     * @return array The array with the host's directories
     */
    public function getDirectories()
    {
        return array(
            DirectoryKeys::TMP     => $this->getTmpBase(),
            DirectoryKeys::DEPLOY  => $this->getDeployBase(),
            DirectoryKeys::WEBAPPS => $this->getAppBase()
        );
    }
}
