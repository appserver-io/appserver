<?php

/**
 * TechDivision\ApplicationServer\Api\RecursiveNormalizer
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
 * Normalizes configuration nodes recursive to \stdClass instances.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class RecursiveNormalizer extends Normalizer
{

    /**
     * Normalizes the passed configuration node recursive and returns
     * a \stdClass representation of it.
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration
     *            The configuration node to normalize recursive
     * @return \stdClass The normalized configuration node
     */
    public function normalize(Configuration $configuration)
    {

        // normalize the configuration node without children
        $node = parent::normalize($configuration);

        // now we add recursive normalization
        foreach ($configuration->getChildren() as $child) {
            $node->{$configuration->getNodeName()}->children[] = $this->normalize($child);
        }

        // return the normalized node instance
        return $node;
    }
}
