<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface
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
 * The interface for the class loader configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface ClassLoaderNodeInterface extends NodeInterface
{

    /**
     * Returns the class loader name.
     *
     * @return string The unique application name
     */
    public function getName();

    /**
     * Returns the class loader type.
     *
     * @return string The class name
     */
    public function getType();

    /**
     * Array with the directories.
     *
     * @return array
     */
    public function getDirectories();

    /**
     * The environment to use, can be one of 'development' or 'production'.
     *
     * @return string The configured environment
     */
    public function getEnvironment();

    /**
     * Flag that shows PBC type safety is activated.
     *
     * @return boolean TRUE if PBC type safety is enabled, else FALSE
     */
    public function getTypeSafety();

    /**
     * The processing level to use, can be one of 'exception' or 'logging'.
     *
     * @return string The processing level
     */
    public function getProcessing();

    /**
     * The PBC enforcement level to use.
     *
     * @return integer The enforcement level
     */
    public function getEnforcementLevel();
}
