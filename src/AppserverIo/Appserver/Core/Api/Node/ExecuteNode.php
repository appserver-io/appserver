<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\ExecuteNode
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
 * DTO to transfer information about a script that has to be executed.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ExecuteNode extends AbstractArgsNode
{

    /**
     * The script to be executed.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $script;

    /**
     * Returns the step type
     *
     * @return string The step type
     */
    public function getScript()
    {
        return $this->script;
    }
}
