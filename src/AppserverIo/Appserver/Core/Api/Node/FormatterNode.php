<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\FormatterNode
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

/**
 * DTO to transfer formatter information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class FormatterNode extends AbstractNode
{

    /**
     * A params node trait.
     *
     * @var \TraitInterface
     */
    use ParamsNodeTrait;

    /**
     * The formatters class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Initializes the formatter node with the necessary data.
     *
     * @param string $type   The formatters class name
     * @param array  $params The formatter params
     */
    public function __construct($type = '', array $params = array())
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->type = $type;
        $this->params = $params;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \AppserverIo\Appserver\Core\Api\Node\AbstractNode::getPrimaryKey()
     */
    public function getPrimaryKey()
    {
        return $this->getType();
    }

    /**
     * Returns information about the formatters class name.
     *
     * @return string The formatters class name
     */
    public function getType()
    {
        return $this->type;
    }
}
