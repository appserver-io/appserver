<?php
/**
 * TechDivision\ApplicationServer\Api\Node\ClassLoaderNode
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

use TechDivision\ApplicationServer\Utilities\ClassLoaderKeys;

/**
 * DTO to transfer a app.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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
     * The unique application name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The class loader class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Initializes the class loader configuration with the passed values.
     *
     * @param string $name        The unique manager name
     * @param string $type        The manager class name
     * @param array  $params      The class loaders params
     * @param array  $directories The class loaders directory to load classes from
     * @param array  $namespaces  The class loaders namespaces for classes to be handled
     */
    public function __construct($name = '', $type = '', array $params = array(), array $directories = array(), array $namespaces = array())
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->name = $name;
        $this->type = $type;
        $this->params = $params;
        $this->directories = $directories;
        $this->namespaces = $namespaces;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \TechDivision\ApplicationServer\Api\Node\AbstractNode::getPrimaryKey()
     */
    public function getPrimaryKey()
    {
        return $this->getName();
    }

    /**
     * Returns the class loader name.
     *
     * @return string The unique application name
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
     * The environment to use, can be one of 'development' or 'production'.
     *
     * @return string The configured environment
     */
    public function getEnvironment()
    {
        return $this->getParam(ClassLoaderKeys::ENVIRONMENT);
    }

    /**
     * Flag that shows PBC type safety is activated.
     *
     * @return boolean TRUE if PBC type safety is enabled, else FALSE
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
     * The PBC enforcement level to use.
     *
     * @return integer The enforcement level
     */
    public function getEnforcementLevel()
    {
        return $this->getParam(ClassLoaderKeys::ENFORCEMENT_LEVEL);
    }
}
