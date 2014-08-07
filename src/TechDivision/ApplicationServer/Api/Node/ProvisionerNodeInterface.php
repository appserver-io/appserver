<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ProvisionerNodeInterface
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
 * Interface for the provisioner node information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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
