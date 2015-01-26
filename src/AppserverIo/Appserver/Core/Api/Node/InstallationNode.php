<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\InstallationNode
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
 * DTO to transfer a the installation information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class InstallationNode extends AbstractNode
{

    /**
     * The installation steps.
     *
     * @var array
     * @AS\Mapping(nodeName="steps/step", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\StepNode")
     */
    protected $steps;

    /**
     * Sets the installation steps.
     *
     * @param array $steps The installation steps
     *
     * @return void
     */
    public function setSteps(array $steps)
    {
        $this->steps = $steps;
    }

    /**
     * Returns the installation step.
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->steps;
    }
}
