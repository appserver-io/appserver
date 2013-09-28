<?php

/**
 * TechDivision\ApplicationServer\Api\Normalizer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Configuration;

/**
 * Normalizes configuration nodes to \stdClass instances.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class Normalizer implements NormalizerInterface
{

    /**
     * Normalizes the passed configuration node and returns a \stdClass
     * representation of it.
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration
     *            The configuration node to normalize
     * @return \stdClass The normalized configuration node
     */
    public function normalize(Configuration $configuration)
    {

        // initialize the \stdClass instance
        $node = new \stdClass();
        $node->{$configuration->getNodeName()} = new \stdClass();

        // set the node value if available
        if ($value = $configuration->getValue()) {
            $node->{$configuration->getNodeName()}->value = $value;
        }

        // set members by converting camel case to underscore (necessary for ember.js)
        foreach ($configuration->getAllData() as $member => $value) {
            $node->{$configuration->getNodeName()}->{strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $member))} = $value;
        }

        // return the normalized node instance
        return $node;
    }
}