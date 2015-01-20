<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\LocationNode
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
 * DTO to transfer location information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class LocationNode extends AbstractNode
{

    /**
     * The file handler node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\FileHandlersNodeTrait
     */
    use FileHandlersNodeTrait;

    /**
     * The condition to match for.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $condition;

    /**
     * Returns the condition to match for.
     *
     * @return string The condition to match for
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Converts the location node into an assoziative array
     * and returns it.
     *
     * @return array The array with the location node data
     */
    public function toArray()
    {
        return array(
            'condition' => $this->getCondition(),
            'handlers' => $this->getFileHandlersAsArray()
        );
    }
}
