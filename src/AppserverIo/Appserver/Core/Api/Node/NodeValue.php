<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\NodeValue
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

use TechDivision\Configuration\Interfaces\ValueInterface;
use TechDivision\Configuration\Interfaces\ConfigurationInterface;

/**
 * Represents a node's value.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class NodeValue implements ValueInterface
{

    /**
     * The nodes value.
     *
     * @var string
     */
    protected $value;

    /**
     * Initializes the node with the value.
     *
     * @param \TechDivision\Configuration\Interfaces\ConfigurationInterface $configuration The configuration instance
     *
     * @return void
     */
    public function initFromConfiguration(ConfigurationInterface $configuration)
    {
        $this->value = $configuration->getValue();
    }

    /**
     * Initializes the node with the passed data.
     *
     * @param string $value The value to initialize the node with
     */
    public function __construct($value = '')
    {
        $this->value = $value;
    }

    /**
     * Set's the node's value.
     *
     * @param string $value The value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Return's the node value.
     *
     * @return string The node value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Implements toString method
     *
     * @return string
     * @see \AppserverIo\Appserver\Core\Api\Node\NodeValue::getValue()
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
