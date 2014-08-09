<?php
/**
 * TechDivision\ApplicationServer\Api\Node\DescriptionNode
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

use TechDivision\Configuration\Interfaces\ValueInterface;

/**
 * DTO to transfer a simple description node.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DescriptionNode extends AbstractValueNode
{

    /**
     * Initializes the param node with the necessary data.
     *
     * @param \TechDivision\Configuration\Interfaces\ValueInterface $nodeValue The params initial value
     */
    public function __construct(ValueInterface $nodeValue = null)
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->nodeValue = $nodeValue;
    }
}
