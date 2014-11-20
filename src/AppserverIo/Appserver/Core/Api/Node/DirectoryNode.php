<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\DirectoryNode
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
 * DTO to transfer the directory information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
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
