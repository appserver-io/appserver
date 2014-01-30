<?php

/**
 * TechDivision\ApplicationServer\Api\Node\AbstractNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

use Herrera\Annotations\Tokens;
use Herrera\Annotations\Tokenizer;
use Herrera\Annotations\Convert\ToArray;
use Rhumsaa\Uuid\Uuid;
use TechDivision\ApplicationServer\Configuration;
use TheSeer\Autoload\Config;

/**
 * DTO to transfer aliases.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
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

    public function initFromFile($filename)
    {
        $configuration = new Configuration();
        $configuration->initFromFile($filename);
        $this->initFromConfiguration($configuration);
    }

    public function initFromConfiguration(Configuration $configuration)
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

    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;
    }

    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     *
     */
    public function newUuid()
    {
        return Uuid::uuid4()->__toString();
    }

    public function getConfigurationNodeName($configuration, $mapping)
    {

        $parts = array();
        $parts[] = $configuration->getNodeName();

        if ($part = $mapping->getNodeName()) {
            $parts[] = $part;
        }

        return '/' . implode('/', $parts);
    }

    public function isValueClass($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->implementsInterface('TechDivision\ApplicationServer\Api\Node\ValueInterface');
    }

    public function getValueForReflectionProperty($reflectionProperty, $configuration)
    {
        $mapping = $this->getPropertyTypeFromDocComment($reflectionProperty);

        if ($mapping == null) {
            return;
        }

        $nodeType = $mapping->getNodeType();
        $configurationNodeName = $this->getConfigurationNodeName($configuration, $mapping);

        if (class_exists($nodeType) && $this->isValueClass($nodeType)) {

            // return new $nodeType($configuration, $this->getUuid());

            $newNode = new $nodeType();
            $newNode->initFromConfiguration($configuration);

            return $this->{$reflectionProperty->getName()} = $newNode;

        } elseif (class_exists($nodeType)) {

            // return new $nodeType($configuration->getChild($configurationNodeName), $this->getUuid());

            $newNode = new $nodeType();

            if ($child = $configuration->getChild($configurationNodeName)) {
                $newNode->initFromConfiguration($child);
            }

            $newNode->setParentUuid($this->getUuid());

            return $this->{$reflectionProperty->getName()} = $newNode;

        }

        if (in_array($nodeType, array('integer', 'string', 'double', 'float', 'boolean'))) {
            return $this->{$reflectionProperty->getName()} = $configuration->getData($reflectionProperty->getName());
        }

        if ($nodeType == 'array') {

            $result = array();

            foreach ($configuration->getChilds($configurationNodeName) as $child) {

                $elementType = $mapping->getElementType();
                $newNode = new $elementType();
                $newNode->initFromConfiguration($child);
                $newNode->setParentUuid($this->getUuid());

                $this->{$reflectionProperty->getName()}[$newNode->getPrimaryKey()] = $newNode;

                /*
                $elementType = $mapping->getElementType();
                $element = new $elementType($child, $this->getUuid());
                $result[$element->getUuid()] = $element;
                */
            }

            return $result;
        }

        throw new \Exception(sprintf("Found invalid property type %s in node %s", $nodeType, get_class($this)));
    }

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

    public function setConfigurationByReflectionProperty($reflectionProperty, $configuration)
    {
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

    public function appendConfigurationChild($reflectionProperty, $configuration, $path)
    {

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
     * @param \ReflectionProperty $reflectionProperty
     *            The property to return the type for
     * @throws \Exception Is thrown if the property has NO bean annotation
     * @return \TechDivision\ApplicationServer\Api\Node\Mapping The found property type mapping
     */
    public function getPropertyTypeFromDocComment($reflectionProperty)
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
