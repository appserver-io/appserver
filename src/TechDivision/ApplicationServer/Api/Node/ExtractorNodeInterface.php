<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ExtractorNodeInterface
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
 * Interface for the extractor node information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface ExtractorNodeInterface extends NodeInterface
{

    /**
     * Returns the extractor type.
     *
     * @return string The server's type
     */
    public function getType();

    /**
     * Returns the extractor name.
     *
     * @return string The extractor name
     */
    public function getName();

    /**
     * Returns the flag that backups should be created.
     *
     * @return boolean The flag to create backups
     */
    public function isCreateBackups();

    /**
     * Returns the flag that backups should be restored.
     *
     * @return boolean The flag to restore backups
     */
    public function isRestoreBackups();
}
