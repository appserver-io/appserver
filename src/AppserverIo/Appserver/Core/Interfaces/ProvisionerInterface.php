<?php
/**
 * AppserverIo\Appserver\Core\Interfaces\ProvisionerInterface
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

namespace AppserverIo\Appserver\Core\Interfaces;

/**
 * An provisioner interface
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface ProvisionerInterface
{

    /**
     * Provisions all web applications.
     *
     * @return void
     */
    public function provision();

    /**
     * Returns the provisioner node configuration data.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ProvisionerNodeInterface The provisioner node configuration data
     */
    public function getProvisionerNode();
}
