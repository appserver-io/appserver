<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\StepNode
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
 * DTO to transfer a applications provision configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class StepNode extends AbstractNode
{
    // We want to use params here.
    use ParamsNodeTrait;

    /**
     * The step type
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The node containing the information to execute something like a script.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ExecuteNode
     * @AS\Mapping(nodeName="execute", nodeType="AppserverIo\Appserver\Core\Api\Node\ExecuteNode")
     */
    protected $execute;

    /**
     * Returns the step type
     *
     * @return string The step type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the node containing installation information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InstallationNode The node containing installation information
     */
    public function getExecute()
    {
        return $this->execute;
    }
}
