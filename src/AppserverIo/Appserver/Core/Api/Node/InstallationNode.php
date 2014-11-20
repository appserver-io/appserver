<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\InstallationNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer a the installation information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
