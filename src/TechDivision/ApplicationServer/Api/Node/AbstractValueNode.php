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
     * @param \TechDivision\ApplicationServer\Api\Node\ValueInterface $nodeValue The node value to set
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
     * @return \TechDivision\ApplicationServer\Api\Node\NodeValue The node's value
     * @see \TechDivision\ApplicationServer\Api\Node\NodeValueInterface::getNodeValue()
     */
    public function getNodeValue()
    {
        return $this->nodeValue;
    }
}
