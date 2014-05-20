<?php
/**
 * TechDivision\ApplicationServer\Api\Node\StepNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a applications provision configuration.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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
     * @var \TechDivision\ApplicationServer\Api\Node\ExecuteNode
     * @AS\Mapping(nodeName="execute", nodeType="TechDivision\ApplicationServer\Api\Node\ExecuteNode")
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
     * @return \TechDivision\ApplicationServer\Api\Node\InstallationNode The node containing installation information
     */
    public function getExecute()
    {
        return $this->execute;
    }
}
