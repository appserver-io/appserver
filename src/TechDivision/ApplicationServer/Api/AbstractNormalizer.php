<?php

/**
 * TechDivision\ApplicationServer\Api\AbstractNormalizer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;

/**
 * Normalizes configuration nodes to \stdClass instances.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractNormalizer implements NormalizerInterface
{

    /**
     * The initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext;
     */
    protected $initialContext;

    /**
     * Initializes the normalizer with the initial context.
     *
     * @param InitialContext $initalContext
     *            The initial context instance
     * @param ServiceInterface $service
     *            The service to normalize for
     * @return void
     */
    public function __construct(InitialContext $initalContext, ServiceInterface $service)
    {
        $this->initialContext = $initalContext;
        $this->service = $service;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\NormalizerInterface::getInitialContext()
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\NormalizerInterface::getService()
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }
}