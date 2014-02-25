<?php
/**
 * TechDivision\ApplicationServer\Api\Node\Mapping
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
class Mapping
{

    /**
     * The tokens name
     * @var string
     */
    protected $name;

    /**
     * The node type
     * @var string
     */
    protected $nodeType;

    /**
     * The node name
     * @var string
     */
    protected $nodeName;

    /**
     * The element type
     * @var string
     */
    protected $elementType;

    /**
     * The attach method
     * @var string
     */
    protected $attachMethod;

    /**
     * Construct
     *
     * @param \stdClass $token A simple token object
     */
    public function __construct(\stdClass $token)
    {
        $this->name = $token->name;

        $this->attachMethod = "attach{ucfirst($this->name)}";

        foreach ($token->values as $member => $value) {
            $this->$member = $value;
        }
    }

    /**
     * Return's the token name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return's the node type
     *
     * @return string
     */
    public function getNodeType()
    {
        return $this->nodeType;
    }

    /**
     * Return's the node name
     *
     * @return string
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * Returns the element type
     *
     * @return string
     */
    public function getElementType()
    {
        return $this->elementType;
    }

    /**
     * Returns the attach method
     *
     * @return string
     */
    public function getAttachMethod()
    {
        return $this->attachMethod;
    }
}
