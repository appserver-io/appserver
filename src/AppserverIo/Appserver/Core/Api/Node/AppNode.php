<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\AppNode
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
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\ApplicationServer\Configuration\AppConfigurationInterface;

/**
 * DTO to transfer an app.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AppNode extends AbstractNode implements AppConfigurationInterface
{

    /**
     * The unique XML configuration node name for a app node.
     *
     * @var string
     */
    const NODE_NAME = 'application';

    /**
     * Default constructor
     *
     * @param string $name       Name of the webapp
     * @param string $webappPath Path to the webapp
     */
    public function __construct($name = '', $webappPath = '')
    {
        // initialize the UUID and node name
        $this->setUuid($this->newUuid());
        $this->setNodeName(self::NODE_NAME);

        // set the data
        $this->name = $name;
        $this->webappPath = $webappPath;
    }

    /**
     * The unique application name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The application's path.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $webappPath;

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
     * Returns the application's path.
     *
     * @return string The application's path
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Will initialize an existing app node from a given application
     *
     * @param ApplicationInterface $application The application to init from
     *
     * @return null
     */
    public function initFromApplication(ApplicationInterface $application)
    {
        $this->setNodeName(self::NODE_NAME);
        $this->name = $application->getName();
        $this->webappPath = $application->getWebappPath();
        $this->setUuid($this->newUuid());
    }
}
