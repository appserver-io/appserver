<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\ClassLoaderNode
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

use AppserverIo\Appserver\Core\Utilities\ClassLoaderKeys;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\EnterpriseBeans\Annotations\AnnotationKeys;

/**
 * DTO to transfer a app.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ClassLoaderNode extends AbstractNode implements ClassLoaderNodeInterface
{

    /**
     * A params node trait.
     *
     * @var \TraitInterface
     */
    use ParamsNodeTrait;

    /**
     * A directories node trait.
     *
     * @var \TraitInterface
     */
    use DirectoriesNodeTrait;

    /**
     * A namespaces node trait.
     *
     * @var \TraitInterface
     */
    use NamespacesNodeTrait;

    /**
     * The interface name the class loader has.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $interface;

    /**
     * The unique class loader name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The class loaders class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The class loaders factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * Initializes the class loader configuration with the passed values.
     *
     * @param string $name        The unique class loader name
     * @param string $interface   The interface name the class loader has
     * @param string $type        The class loaders class name
     * @param string $factory     The class loaders factory class name
     * @param array  $params      The class loaders params
     * @param array  $directories The class loaders directory to load classes from
     * @param array  $namespaces  The class loaders namespaces for classes to be handled
     */
    public function __construct($name = '', $interface = '', $type = '', $factory = '', array $params = array(), array $directories = array(), array $namespaces = array())
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->name = $name;
        $this->interface = $interface;
        $this->type = $type;
        $this->factory = $factory;
        $this->params = $params;
        $this->directories = $directories;
        $this->namespaces = $namespaces;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \AppserverIo\Appserver\Core\Api\Node\AbstractNode::getPrimaryKey()
     */
    public function getPrimaryKey()
    {
        return $this->getName();
    }

    /**
     * Returns the class loader name.
     *
     * @return string The unique class loader name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the class loader type.
     *
     * @return string The class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the factory class name.
     *
     * @return string The factory class name
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Returns interface name the class loader has.
     *
     * @return string The interface name the class loader has
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * The environment to use, can be one of 'development' or 'production'.
     *
     * @return string The configured environment
     */
    public function getEnvironment()
    {
        return $this->getParam(ClassLoaderKeys::ENVIRONMENT);
    }

    /**
     * Flag that shows Doppelgaenger type safety is activated.
     *
     * @return boolean TRUE if Doppelgaenger type safety is enabled, else FALSE
     */
    public function getTypeSafety()
    {
        return $this->getParam(ClassLoaderKeys::TYPE_SAFETY);
    }

    /**
     * The processing level to use, can be one of 'exception' or 'logging'.
     *
     * @return string The processing level
     */
    public function getProcessing()
    {
        return $this->getParam(ClassLoaderKeys::PROCESSING);
    }

    /**
     * The Doppelgaenger enforcement level to use.
     *
     * @return integer The enforcement level
     */
    public function getEnforcementLevel()
    {
        return $this->getParam(ClassLoaderKeys::ENFORCEMENT_LEVEL);
    }
}
