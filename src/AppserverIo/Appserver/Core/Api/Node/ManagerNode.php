<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\ManagerNode
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

use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Resource;
use AppserverIo\Psr\EnterpriseBeans\Annotations\AnnotationKeys;
use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;

/**
 * DTO to transfer a manager.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ManagerNode extends AbstractNode implements ManagerConfigurationInterface
{
    /**
     * The params trait.
     *
     * @var \Trait
     */
    use ParamsNodeTrait;

    /**
     * A directories node trait.
     *
     * @var \TraitInterface
     */
    use DirectoriesNodeTrait;

    /**
     * The unique manager name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The manager class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The managers factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * The bean class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $beanName;

    /**
     * The mapped class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $mappedName;

    /**
     * The bean interface name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $beanInterface;

    /**
     * The beans fully qualified lookup name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $lookup;

    /**
     * Initializes the manager configuration with the passed values.
     *
     * @param string $name          The unique manager name
     * @param string $type          The manager class name
     * @param string $factory       The managers factory class name
     * @param string $beanName      The bean class name
     * @param string $mappedName    The mapped class name
     * @param string $beanInterface The bean interface name
     * @param string $lookup        The beans fully qualified lookup name
     */
    public function __construct($name = '', $type = '', $factory = '', $beanName = '', $mappedName = '', $beanInterface = '', $lookup = '')
    {
        $this->name = $name;
        $this->type = $type;
        $this->factory = $factory;
        $this->beanName = $beanName;
        $this->mappedName = $mappedName;
        $this->beanInterface = $beanInterface;
        $this->lookup = $lookup;
    }

    /**
     * Returns the application name.
     *
     * @return string The unique application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the class name.
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
     * Returns the bean class name.
     *
     * @return string The bean class name
     */
    public function getBeanName()
    {
        return $this->beanName;
    }

    /**
     * Returns the mapped class name.
     *
     * @return string The mapped class name
     */
    public function getMappedName()
    {
        return $this->mappedName;
    }

    /**
     * Returns the bean interface name.
     *
     * @return string The bean interface name
     */
    public function getBeanInterface()
    {
        return $this->beanInterface;
    }

    /**
     * Returns the beans fully qualified PNDI lookup name.
     *
     * @return string The beans fully qualified lookup name
     */
    public function getLookup()
    {
        return $this->lookup;
    }

    /**
     * Returns the managers ENC lookup names found in the configuration, merge with the annotation
     * values, whereas the configuration values will override the annotation values.
     *
     * @return array The array with the managers lookup names
     */
    public function toLookupNames()
    {

        // create a new reflection object of the manager instance
        $reflectionClass = new ReflectionClass($this->getType());

        // initialize the lookup names and the name attribute with the short class name
        $lookupNames = array();
        $lookupNames[AnnotationKeys::NAME] = $reflectionClass->getShortName();

        // check if we've an @Resource annotation
        if ($reflectionClass->hasAnnotation(Resource::ANNOTATION)) {

            // load the @Resource annotation
            $reflectionAnnotation = $reflectionClass->getAnnotation(Resource::ANNOTATION);
            $resourceAnnotation = $reflectionAnnotation->newInstance($reflectionAnnotation->getAnnotationName(), $reflectionAnnotation->getValues());

            // set the name attribute @Resource(name="****")
            if ($name = $resourceAnnotation->getName()) {
                $lookupNames[AnnotationKeys::NAME] = $name;
            }

            // set the name attribute @Resource(beanName="****")
            if ($beanName = $resourceAnnotation->getBeanName()) {
                $lookupNames[AnnotationKeys::BEAN_NAME] = $beanName;
            }

            // set the name attribute @Resource(mappedName="****")
            if ($mappedName = $resourceAnnotation->getMappedName()) {
                $lookupNames[AnnotationKeys::MAPPED_NAME] = $mappedName;
            }

            // set the name attribute @Resource(beanInterface="****")
            if ($beanInterface = $resourceAnnotation->getBeanInterface()) {
                $lookupNames[AnnotationKeys::BEAN_INTERFACE] = $beanInterface;
            }

            // set the name attribute @Resource(lookup="****")
            if ($lookup = $resourceAnnotation->getLookup()) {
                $lookupNames[AnnotationKeys::LOOKUP] = $lookup;
            }
        }

        // overwrite the name attribute from the configuration, if given
        if ($name = $this->getName()) {
            $lookupNames[AnnotationKeys::NAME] = $name;
        }

        // overwrite the beanName attribute from the configuration, if given
        if ($beanName = $this->getBeanName()) {
            $lookupNames[AnnotationKeys::BEAN_NAME] = $beanName;
        }

        // overwrite the mappedName attribute from the configuration, if given
        if ($mappedName = $this->getMappedName()) {
            $lookupNames[AnnotationKeys::MAPPED_NAME] = $mappedName;
        }

        // overwrite the beanInterface attribute from the configuration, if given
        if ($beanInterface = $this->getBeanInterface()) {
            $lookupNames[AnnotationKeys::BEAN_INTERFACE] = $beanInterface;
        }

        // overwrite the lookup attribute from the configuration, if given
        if ($lookup = $this->getLookup()) {
            $lookupNames[AnnotationKeys::LOOKUP] = $lookup;
        }

        // return the lookup names
        return $lookupNames;
    }
}
