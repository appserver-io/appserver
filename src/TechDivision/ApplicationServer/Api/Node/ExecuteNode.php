<?php
/**
 * TechDivision\ApplicationServer\Api\Node\ExecuteNode
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
 * DTO to transfer information about a script that has to be executed.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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
