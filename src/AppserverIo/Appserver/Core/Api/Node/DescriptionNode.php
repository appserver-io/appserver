<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\DescriptionNode
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

use AppserverIo\Configuration\Interfaces\ValueInterface;

/**
 * DTO to transfer a simple description node.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DescriptionNode extends AbstractValueNode
{

    /**
     * Initializes the param node with the necessary data.
     *
     * @param \AppserverIo\Configuration\Interfaces\ValueInterface $nodeValue The params initial value
     */
    public function __construct(ValueInterface $nodeValue = null)
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->nodeValue = $nodeValue;
    }
}
