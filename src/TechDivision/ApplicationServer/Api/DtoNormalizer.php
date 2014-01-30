<?php

/**
 * TechDivision\ApplicationServer\Api\DtoNormalizer
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
 * Normalizes configuration nodes to DTO instances.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class DtoNormalizer extends AbstractNormalizer
{

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\NormalizerInterface::normalize()
     */
    public function normalize(Configuration $configuration)
    {
        $nodeType = $this->getService()->getNodeType();
        return $this->newInstance($nodeType, array(
            $configuration
        ));
    }
}
