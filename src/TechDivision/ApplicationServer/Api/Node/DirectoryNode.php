<?php
/**
 * TechDivision\ApplicationServer\Api\Node\DirectoryNode
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
 * DTO to transfer the directory information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DirectoryNode extends AbstractValueNode
{

    /**
     * The flag to enforce design-by-contract type checking on classes of this directory.
     *
     * @var string
     * @AS\Mapping(nodeType="boolean")
     */
    protected $enforced;

    /**
     * Returns the enforcement flag.
     *
     * @return boolean The enforcement flag
     */
    public function isEnforced()
    {
        return $this->enforced;
    }
}
