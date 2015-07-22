<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AbstractNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use Rhumsaa\Uuid\Uuid;
use Herrera\Annotations\Tokens;
use Herrera\Annotations\Tokenizer;
use Herrera\Annotations\Convert\ToArray;
use AppserverIo\Configuration\Configuration;
use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Configuration\Interfaces\ConfigurationInterface;
use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Lang\Integer;
use AppserverIo\Lang\Float;

/**
 * Abstract node class.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
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
     * @param \AppserverIo\Configuration\Interfaces\ConfigurationInterface $configuration The configuration instance
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

        // set the node name from the configuration
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
     * Returns the virtual ID applied by API services.
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
     * Returns the unique virtual ID of the parent node.
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
     * Returns the node's name
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
     * Returns the configuration node name by given mapping and configuration
     *
     * @param \AppserverIo\Configuration\Interfaces\ConfigurationInterface $configuration The configuration instance
     * @param \AppserverIo\Appserver\Core\Api\Node\Mapping                 $mapping       The mapping instance
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
        return $reflectionClass->implementsInterface('AppserverIo\Configuration\Interfaces\ValueInterface');
    }

    /**
     * Returns the value for a given reflection property and configuration
     *
     * @param \ReflectionProperty                                          $reflectionProperty The reflection property
     * @param \AppserverIo\Configuration\Interfaces\ConfigurationInterface $configuration      The configuration instance
     *
     * @return array|null An array with all values from given reflection property
     * @throws \Exception
     */
    public function getValueForReflectionProperty(\ReflectionProperty $reflectionProperty, ConfigurationInterface $configuration)
    {

        // load the mapping from the annotation
        $mapping = $this->getPropertyTypeFromDocComment($reflectionProperty);

        // if we don't have a mapping do nothing
        if ($mapping == null) {
            return;
        }

        // load node type and configuration node name
        $nodeType = $mapping->getNodeType();
        $configurationNodeName = $this->getConfigurationNodeName($configuration, $mapping);

        // get our valid simple node types
        $simpleTypes = array_flip(array('string', 'integer', 'float', 'boolean', 'double', 'array'));

        // initialize a new value configuration node
        if ((!isset($simpleTypes[$nodeType]) && class_exists($nodeType)) && $this->isValueClass($nodeType)) {
            // initialize the new node type
            /** @var \AppserverIo\Appserver\Core\Api\Node\AbstractNode $newNode */
            $newNode = new $nodeType();
            $newNode->initFromConfiguration($configuration);

            // set the instance
            return $this->{$reflectionProperty->getName()} = $newNode;

        // initialize a new configuration node from the found child data
        } elseif (!isset($simpleTypes[$nodeType]) && class_exists($nodeType)) {
            // first we've to check if the child has data
            if ($child = $configuration->getChild($configurationNodeName)) {
                // initialize the new node type
                /** @var \AppserverIo\Appserver\Core\Api\Node\AbstractNode $newNode */
                $newNode = new $nodeType();
                $newNode->initFromConfiguration($child);
                $newNode->setParentUuid($this->getUuid());

                // set the instance
                $this->{$reflectionProperty->getName()} = $newNode;
            }

            // return anyway
            return;
        }

        // array => create the configured nodes and add them
        if ($nodeType === 'array') {
            // iterate over all elements and create the node
            foreach ($configuration->getChilds($configurationNodeName) as $child) {
                // initialize the node and load the data from the configuration
                $elementType = $mapping->getElementType();
                /** @var \AppserverIo\Appserver\Core\Api\Node\AbstractNode $newNode */
                $newNode = new $elementType();
                $newNode->initFromConfiguration($child);
                $newNode->setParentUuid($this->getUuid());

                // add the value to the node
                if ($pk = $newNode->getPrimaryKey()) {
                    $this->{$reflectionProperty->getName()}[$pk] = $newNode;
                } else {
                    $this->{$reflectionProperty->getName()}[] = $newNode;
                }
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
     * @return \AppserverIo\Configuration\Interfaces\ConfigurationInterface The configuraton instance
     */
    public function exportToConfiguration()
    {

        // create a new configuration instance
        $configuration = new Configuration();
        $configuration->setNodeName($this->getNodeName());
        $configuration->setData('uuid', $this->getUuid());

        // iterate over the PROTECTED properties and initialize them with the configuration data
        $reflectionObject = new \ReflectionObject($this);

        // iterate over all members and add their values to the configuration
        foreach ($reflectionObject->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionProperty) {
            // ONLY use PROTECTED properties, NOT PRIVATE, else UUID's will be overwritten!!
            $this->setConfigurationByReflectionProperty($reflectionProperty, $configuration);
        }

        // return the configuration instance
        return $configuration;
    }

    /**
     * Sets the configuration by reflected property.
     *
     * @param \ReflectionProperty                                          $reflectionProperty The reflection property to set
     * @param \AppserverIo\Configuration\Interfaces\ConfigurationInterface $configuration      The configuration instance
     *
     * @return \AppserverIo\Configuration\Interfaces\ConfigurationInterface|void The configuration or nothing
     */
    public function setConfigurationByReflectionProperty(
        \ReflectionProperty $reflectionProperty,
        ConfigurationInterface $configuration
    ) {

        // load the mapping from the annotation
        $mapping = $this->getPropertyTypeFromDocComment($reflectionProperty);

        // if the mapping OR the property itself is NULL, do nothing
        if ($mapping == null || $this->{$reflectionProperty->getName()} == null) {
            return;
        }

        // load the mappings node type
        $nodeType = $mapping->getNodeType();

        // if we have a node or a node value, export the data
        if (class_exists($nodeType) && $this->isValueClass($nodeType)) {
            return $configuration->setValue($this->{$reflectionProperty->getName()}->getValue());
        } elseif (class_exists($nodeType)) {
            return $configuration->addChild($this->{$reflectionProperty->getName()}->exportToConfiguration());
        }

        // if we have simple data type, export the value
        if (in_array($nodeType, array('integer', 'string', 'double', 'float', 'boolean'))) {
            return $configuration->setData($reflectionProperty->getName(), $this->{$reflectionProperty->getName()});
        }

        // if we have an array, export the array data
        if ($nodeType == 'array' && sizeof($this->{$reflectionProperty->getName()}) > 0) {
            return $this->appendConfigurationChild($reflectionProperty, $configuration, $mapping->getNodeName());
        }
    }

    /**
     * Appends the value of the passed reflection property to the
     * configuration under the also passed path.
     *
     * @param \ReflectionProperty                                          $reflectionProperty The reflection property
     * @param \AppserverIo\Configuration\Interfaces\ConfigurationInterface $configuration      The configuration instance
     * @param string                                                       $path               A path were to append to
     *
     * @return void
     * @throws \Exception
     */
    public function appendConfigurationChild(
        \ReflectionProperty $reflectionProperty,
        ConfigurationInterface $configuration,
        $path
    ) {

        // tokenize the we want to append the configuration
        $token = strtok($path, '/');
        $next = substr($path, strlen('/' . $token));

        // if we can't find the specified path in that instance
        if (!empty($token) && !empty($next)) {
            // initialize the configuration value
            $child = new Configuration();
            $child->setNodeName($token);

            // add it to this instance
            $this->appendConfigurationChild($reflectionProperty, $child, $next);

            // and also add it to the passed configuration
            $configuration->addChild($child);

        // if we can find it
        } elseif (!empty($token) && empty($next)) {
            // only add it the the passed configuration
            foreach ($this->{$reflectionProperty->getName()} as $node) {
                $configuration->addChild($node->exportToConfiguration());
            }

        } else {
            // or throw an exception if the passed path is not valid
            throw new \Exception(sprintf('Found invalid path %s', $path));
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
        $aliases = array('AS' => 'AppserverIo\\Appserver\\Core\\Api\\Node');

        // parse the doc block
        $parsed = $tokenizer->parse($reflectionProperty->getDocComment(), $aliases);

        // convert tokens and return one
        $tokens = new Tokens($parsed);
        $toArray = new ToArray();

        // iterate over the tokens
        foreach ($toArray->convert($tokens) as $token) {
            if ($token->name == 'AppserverIo\\Appserver\\Core\\Api\\Node\\Mapping') {
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
            // un-camelcase property names in stdClass representation
            $stdClass->{ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $propertyName)), '_')} = $this->$propertyName;
        }

        // set the primary key
        $stdClass->id = $this->getPrimaryKey();

        // return the instance
        return $stdClass;
    }
}
