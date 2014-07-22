<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ClassLoaderNodeInterface
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

use TechDivision\Configuration\Interfaces\NodeInterface;

/**
 * The interface for the class loader configuration.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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

    /**
     * The namespaces which are omitted form PBC enforcement.
     *
     * @return array The array of enforcement omitted namespaces
     */
    public function getEnforcementOmit();
}
