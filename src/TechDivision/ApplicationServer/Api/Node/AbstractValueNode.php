<?php
/**
 * TechDivision\ApplicationServer\Api\Node\AbstractNode
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
use TechDivision\Configuration\Interfaces\NodeValueInterface;

/**
 * DTO to transfer aliases.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractValueNode extends AbstractNode implements NodeValueInterface
{

    /**
     * The node value.
     *
     * @var string @AS\Mapping(nodeType="TechDivision\ApplicationServer\Api\Node\NodeValue")
     */
    protected $nodeValue;

    /**
     * Set's the node value instance.
     *
     * @param \TechDivision\Configuration\Interfaces\ValueInterface $nodeValue The node value to set
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
     * @return \TechDivision\Configuration\Interfaces\ValueInterface The node's value
     * @see \TechDivision\Configuration\Interfaces\NodeValueInterface::getNodeValue()
     */
    public function getNodeValue()
    {
        return $this->nodeValue;
    }
}
