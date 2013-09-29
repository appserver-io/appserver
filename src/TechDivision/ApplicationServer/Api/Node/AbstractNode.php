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
use TechDivision\ApplicationServer\Configuration;

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
     *
     * @param Configuration $configuration
     * @throws \Exception Is thrown if the passed configuration node doesn't match the DTO
     */
    public function __construct(Configuration $configuration)
    {

        $reflectionObject = new \ReflectionObject($this);

        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            $propertyValue = $this->getValueForReflectionProperty($reflectionProperty, $configuration);
            $this->{$reflectionProperty->getName()} = $propertyValue;
        }
    }

    public function getNodeName($configuration, $mapping)
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
        $nodeName = $this->getNodeName($configuration, $mapping);

        if (class_exists($nodeType) && $this->isValueClass($nodeType)) {
            return new $nodeType($configuration);
        } elseif (class_exists($nodeType))  {
            return new $nodeType($configuration->getChild($nodeName));
        }

        if (in_array($nodeType, array('integer', 'string', 'double', 'float', 'boolean'))) {
            return $configuration->getData($reflectionProperty->getName());
        }

        if ($nodeType == 'array') {

            $result = array();

            foreach ($configuration->getChilds($nodeName) as $child) {
                $elementType = $mapping->getElementType();
                $result[] = new $elementType($child);
            }

            return $result;
        }

        throw new \Exception(sprintf("Found invalid property type %s", $nodeType));
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
}