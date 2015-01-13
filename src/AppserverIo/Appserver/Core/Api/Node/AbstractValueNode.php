<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\AbstractNode
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
use AppserverIo\Configuration\Interfaces\NodeValueInterface;

/**
 * Abstract class to handle node values
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractValueNode extends AbstractNode implements NodeValueInterface
{

    /**
     * The node value.
     *
     * @var string @AS\Mapping(nodeType="AppserverIo\Appserver\Core\Api\Node\NodeValue")
     */
    protected $nodeValue;

    /**
     * Set's the node value instance.
     *
     * @param \AppserverIo\Configuration\Interfaces\ValueInterface $nodeValue The node value to set
     *
     * @return void
     */
    public function setNodeValue(ValueInterface $nodeValue)
    {
        $this->nodeValue = $nodeValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @return \AppserverIo\Configuration\Interfaces\ValueInterface The node's value
     * @see \AppserverIo\Configuration\Interfaces\NodeValueInterface::getNodeValue()
     */
    public function getNodeValue()
    {
        return $this->nodeValue;
    }
}
