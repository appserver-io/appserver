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

use Rhumsaa\Uuid\Uuid;
use Herrera\Annotations\Tokens;
use Herrera\Annotations\Tokenizer;
use Herrera\Annotations\Convert\ToArray;
use TechDivision\Configuration\Configuration;
use TechDivision\Configuration\Interfaces\NodeInterface;
use TechDivision\Configuration\Interfaces\ConfigurationInterface;
use TechDivision\Lang\String;
use TechDivision\Lang\Boolean;
use TechDivision\Lang\Integer;
use TechDivision\Lang\Float;

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
abstract class AbstractNode implements NodeInterface
{

    /**
     * Virtual ID applied by API services.
     *
     * @var string
     */
    private $uuid;

    /**
     * The unique virtual ID of the parent node.
     *
     * @var string
     */
    private $parentUuid;

    /**
     * The XML node name the node data is from.
     *
     * @var string
     */
    private $nodeName;

    /**
     * Initialise from file
     *
     * @param string $filename The filename as string
     *
     * @return void
     */
    public function initFromFile($filename)
    {
        $configuration = new Configuration();
        $configuration->initFromFile($filename);
        $this->initFromConfiguration($configuration);
    }

    /**
     * Initialise from string
     *
     * @param string $string The string to configure from
     *
     * @return void
     */
    public function initFromString($string)
    {
        $configuration = new Configuration();
        $configuration->initFromString($string);
        $this->initFromConfiguration($configuration);
    }

    /**
     * Initialise from configuration instance
     *
     * @param \TechDivision\Configuration\Interfaces\ConfigurationInterface $configuration The configuration instance
     *
     * @return void
     */
    public function initFromConfiguration(ConfigurationInterface $configuration)
    {
        // create a UUID and set the UUID of the parent node
        if ($configuration->getData('uuid') == null) {
            $this->setUuid($this->newUuid());
        } else {
            $this->setUuid($configuration->getData('uuid'));
        }

        $this->setNodeName($configuration->getNodeName());

        // iterate over the PROTECTED properties and initialize them with the configuration data
        $reflectionObject = new \ReflectionObject($this);

        foreach ($reflectionObject->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            // ONLY use PROTECTED properties, NOT PRIVATE, else UUID's will be overwritten!!
            $this->getValueForReflectionProperty($reflectionProperty, $configuration);
        }
    }

    /**
     * Returns the nodes primary key, the UUID by default.
     *
     * @return string The nodes primary key
     */
    public function getPrimaryKey()
    {
        return $this->getUuid();
    }

    /**
     * Set's the the virtual ID applied by API services.
     *
     * @param string $uuid The the virtual ID applied by API services
     *
     * @return void
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Return's the virtual ID applied by API services.
     *
     * @return string The nodes unique node ID
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set's the unique virtual ID of the parent node.
     *
     * @param string $parentUuid The unique virtual ID of the parent node
     *
     * @return void
     */
    public function setParentUuid($parentUuid)
    {
        $this->parentUuid = $parentUuid;
    }

    /**
     * Return's the unique virtual ID of the parent node.
     *
     * @return string The unique ID of the parent node
     */
    public function getParentUuid()
    {
        return $this->parentUuid;
    }

    /**
     * Set's the node's name
     *
     * @param string $nodeName The node's name
     *
     * @return void
     */
    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;
    }

    /**
     * Return's the node's name
     *
     * @return string The node's name
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * Generates a uuid
     *
     * @return string The generated uuid
     */
    public function newUuid()
    {
        return Uuid::uuid4()->__toString();
    }

    /**
     * Return's the configuration node name by given mapping and configuration
     *
     * @param \TechDivision\Configuration\ConfigurationInterface $configuration The configuration instance
     * @param \TechDivision\ApplicationServer\Api\Node\Mapping   $mapping       The mapping instance
     *
     * @return string
     */
    public function getConfigurationNodeName(ConfigurationInterface $configuration, Mapping $mapping)
    {
        $parts = array();
        $parts[] = $configuration->getNodeName();

        if ($part = $mapping->getNodeName()) {
            $parts[] = $part;
        }

        return '/' . implode('/', $parts);
    }

    /**
     * Checks if given classname is an implementation of ValueInterface
     *
     * @param string $className The class name to check
     *
     * @return bool true if its an implementation of ValueInterface or false if not.
     */
    public function isValueClass($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->implementsInterface('TechDivision\Configuration\Interfaces\ValueInterface');
    }

    /**
     * Return's the value for a given reflection property and configuration
     *
     * @param \ReflectionProperty                                           $reflectionProperty The reflection property
     * @param \TechDivision\Configuration\Interfaces\ConfigurationInterface $configuration      The configuration instance
     *
     * @return array An array with all values from given reflection property
     * @throws \Exception
     */
    public function getValueForReflectionProperty(\ReflectionProperty $reflectionProperty, ConfigurationInterface $configuration)
    {
        $mapping = $this->getPropertyTypeFromDocComment($reflectionProperty);

        if ($mapping == null) {
            return;
        }

        $nodeType = $mapping->getNodeType();
        $configurationNodeName = $this->getConfigurationNodeName($configuration, $mapping);

        if (class_exists($nodeType) && $this->isValueClass($nodeType)) {

            $newNode = new $nodeType();
            $newNode->initFromConfiguration($configuration);

            return $this->{$reflectionProperty->getName()} = $newNode;

        } elseif (class_exists($nodeType)) {

            $newNode = new $nodeType();

            if ($child = $configuration->getChild($configurationNodeName)) {
                $newNode->initFromConfiguration($child);
            }

            $newNode->setParentUuid($this->getUuid());

            return $this->{$reflectionProperty->getName()} = $newNode;

        }

        // array => create the configured nodes and add them
        if ($nodeType === 'array') {

            // iterate over all elements and create the node
            foreach ($configuration->getChilds($configurationNodeName) as $child) {

                // initialize the node and load the data from the configuration
                $elementType = $mapping->getElementType();
                $newNode = new $elementType();
                $newNode->initFromConfiguration($child);
                $newNode->setParentUuid($this->getUuid());

                // add the value to the node
                $this->{$reflectionProperty->getName()}[$newNode->getPrimaryKey()] = $newNode;
            }

            return;
        }

        // check the node type specified in the annotation
        switch ($nodeType) {

            case 'string': // simple string, we don't have to do anything

                if ($configuration->getData($reflectionProperty->getName()) == null) {
                    return;
                }

                return $this->{$reflectionProperty->getName()} = $configuration->getData($reflectionProperty->getName());

            case 'integer': // integer => validate and transform the value

                if ($configuration->getData($reflectionProperty->getName()) == null) {
                    return;
                }

                $integer = Integer::valueOf(new String($configuration->getData($reflectionProperty->getName())));
                return $this->{$reflectionProperty->getName()} = $integer->intValue();

            case 'float': // float => validate and transform the value

                if ($configuration->getData($reflectionProperty->getName()) == null) {
                    return;
                }

                $float = Float::valueOf(new String($configuration->getData($reflectionProperty->getName())));
                return $this->{$reflectionProperty->getName()} = $float->floatValue();

            case 'double': // double => validate and transform the value

                if ($configuration->getData($reflectionProperty->getName()) == null) {
                    return;
                }

                $float = Float::valueOf(new String($configuration->getData($reflectionProperty->getName())));
                return $this->{$reflectionProperty->getName()} = $float->doubleValue();

            case 'boolean': // boolean => validate and transform the value

                if ($configuration->getData($reflectionProperty->getName()) == null) {
                    return;
                }

                $boolean = Boolean::valueOf(new String($configuration->getData($reflectionProperty->getName())));
                return $this->{$reflectionProperty->getName()} = $boolean->booleanValue();

            default: // we don't support other node types

                throw new \Exception(sprintf("Found invalid property type %s in node %s", $nodeType, get_class($this)));

        }
    }

    /**
     * Exports to the configuration.
     *
     * @return \TechDivision\Configuration\Interfaces\ConfigurationInterface The configuraton instance
     */
    public function exportToConfiguration()
    {

        $configuration = new Configuration();
        $configuration->setNodeName($this->getNodeName());

        $configuration->setData('uuid', $this->getUuid());

        // iterate over the PROTECTED properties and initialize them with the configuration data
        $reflectionObject = new \ReflectionObject($this);

        foreach ($reflectionObject->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            // ONLY use PROTECTED properties, NOT PRIVATE, else UUID's will be overwritten!!
            $this->setConfigurationByReflectionProperty($reflectionProperty, $configuration);
        }

        return $configuration;
    }

    /**
     * Sets the configuration by reflected property.
     *
     * @param \ReflectionProperty                                           $reflectionProperty The reflection property to set
     * @param \TechDivision\Configuration\Interfaces\ConfigurationInterface $configuration      The configuration instance
     *
     * @return \TechDivision\Configuration\Interfaces\ConfigurationInterface|void The configuration or nothing
     */
    public function setConfigurationByReflectionProperty(
        \ReflectionProperty $reflectionProperty,
        ConfigurationInterface $configuration
    ) {

        $mapping = $this->getPropertyTypeFromDocComment($reflectionProperty);

        if ($mapping == null) {
            return;
        }

        $nodeType = $mapping->getNodeType();

        if (class_exists($nodeType) && $this->isValueClass($nodeType)) {
            return $configuration->setValue($this->{$reflectionProperty->getName()}->getValue());
        } elseif (class_exists($nodeType)) {
            return $configuration->addChild($this->{$reflectionProperty->getName()}->exportToConfiguration());
        }

        if (in_array($nodeType, array('integer', 'string', 'double', 'float', 'boolean'))) {
            return $configuration->setData($reflectionProperty->getName(), $this->{$reflectionProperty->getName()});
        }

        if ($nodeType == 'array' && sizeof($this->{$reflectionProperty->getName()}) > 0) {
            return $this->appendConfigurationChild($reflectionProperty, $configuration, $mapping->getNodeName());
        }
    }

    /**
     * Appends the configuration on a given path with a given child.
     *
     * @param \ReflectionProperty                                           $reflectionProperty The reflection property
     * @param \TechDivision\Configuration\Interfaces\ConfigurationInterface $configuration      The configuration instance
     * @param string                                                        $path               A path were to append to
     *
     * @return void
     * @throws \Exception
     */
    public function appendConfigurationChild(
        \ReflectionProperty $reflectionProperty,
        ConfigurationInterface $configuration,
        $path
    ) {

        $token = strtok($path, '/');

        $next = substr($path, strlen('/' . $token));

        if (!empty($token) && !empty($next)) {

            $child = new Configuration();
            $child->setNodeName($token);

            $this->appendConfigurationChild($reflectionProperty, $child, $next);

            $configuration->addChild($child);

        } elseif (!empty($token) && empty($next)) {

            foreach ($this->{$reflectionProperty->getName()} as $node) {
                $configuration->addChild($node->exportToConfiguration());
            }

        } else {
            throw new \Exception(sprintf("Found invalid path %s", $path));
        }
    }

    /**
     * Returns the property type found in the properties configuration
     * annotation.
     *
     * @param \ReflectionProperty $reflectionProperty The property to return the type for
     *
     * @throws \Exception Is thrown if the property has NO bean annotation
     * @return Mapping The found property type mapping
     */
    public function getPropertyTypeFromDocComment(\ReflectionProperty $reflectionProperty)
    {

        // initialize the annotation tokenizer
        $tokenizer = new Tokenizer();

        // set the aliases
        $aliases = array('AS' => 'TechDivision\\ApplicationServer\\Api\\Node');

        // parse the doc block
        $parsed = $tokenizer->parse($reflectionProperty->getDocComment(), $aliases);

        // convert tokens and return one
        $tokens = new Tokens($parsed);
        $toArray = new ToArray();

        // iterate over the tokens
        foreach ($toArray->convert($tokens) as $token) {
            if ($token->name == 'TechDivision\\ApplicationServer\\Api\\Node\\Mapping') {
                return new $token->name($token);
            }
        }
    }

    /**
     * Returns the node as stdClass representation.
     *
     * @return \stdClass The node as stdClass representation
     */
    public function toStdClass()
    {
        // initialize a new stdClass representation
        $stdClass = new \stdClass();

        // iterate over the PROTECTED properties and initialize them with the configuration data
        $reflectionObject = new \ReflectionObject($this);
        foreach ($reflectionObject->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            // load property name
            $propertyName = $reflectionProperty->getName();
            // uncamelcase property names in stdClass representation
            $stdClass->{ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $propertyName)), '_')} = $this->$propertyName;
        }

        // set the primary key
        $stdClass->id = $this->getPrimaryKey();

        // return the instance
        return $stdClass;
    }
}
