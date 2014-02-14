<?php
/**
 * TechDivision\ApplicationServer\Api\ServiceInterface
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

namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\ApplicationServer\InitialContext;

/**
 * This interface defines the basic method each API service has
 * to provide.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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
     * @param NodeInterface $systemConfiguration The system configuration
     *
     * @return ServiceInterface
     */
    public function setSystemConfiguration(NodeInterface $systemConfiguration);

    /**
     * Returns the system configuration.
     *
     * @return NodeInterface The system configuration
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
     * @return NodeInterface The node with the UUID passed as parameter
     */
    public function load($uuid);
}
