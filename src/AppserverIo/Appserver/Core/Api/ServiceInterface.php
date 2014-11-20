<?php
/**
 * AppserverIo\Appserver\Core\Api\ServiceInterface
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

namespace AppserverIo\Appserver\Core\Api;

use TechDivision\Configuration\Interfaces\NodeInterface;
use AppserverIo\Appserver\Core\InitialContext;

/**
 * This interface defines the basic method each API service has
 * to provide.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface ServiceInterface
{

    /**
     * Returns the initial context instance.
     *
     * @return InitialContext The initial Context
     */
    public function getInitialContext();

    /**
     * Returns the system configuration.
     *
     * @param \TechDivision\Configuration\Interfaces\NodeInterface $systemConfiguration The system configuration
     *
     * @return ServiceInterface
     */
    public function setSystemConfiguration(NodeInterface $systemConfiguration);

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\Configuration\Interfaces\NodeInterface The system configuration
     */
    public function getSystemConfiguration();

    /**
     * Return's all nodes.
     *
     * @return array An array with all nodes
     */
    public function findAll();

    /**
     * Returns the node with the passed UUID.
     *
     * @param integer $uuid UUID of the node to return
     *
     * @return \TechDivision\Configuration\Interfaces\NodeInterface The node with the UUID passed as parameter
     */
    public function load($uuid);
}
