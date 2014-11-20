<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ProvisionerNodeInterface
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

use TechDivision\Configuration\Interfaces\NodeInterface;

/**
 * Interface for the provisioner node information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface ProvisionerNodeInterface extends NodeInterface
{

    /**
     * Returns the provisioner type.
     *
     * @return string The provisioner type
     */
    public function getType();

    /**
     * Returns the provisioner name.
     *
     * @return string The provisioner name
     */
    public function getName();
}
