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

    // We use several traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
    use ParamsNodeTrait;
    use DirectoriesNodeTrait;
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
     * @param string $name The unique manager name
     * @param string $type The manager class name
     */
    public function __construct($name = '', $type = '')
    {
        $this->name = $name;
        $this->type = $type;
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

    /**
     * The namespaces which are omitted form PBC enforcement.
     *
     * @return array The array of enforcement omitted namespaces
     */
    public function getEnforcementOmit()
    {

    }
}
