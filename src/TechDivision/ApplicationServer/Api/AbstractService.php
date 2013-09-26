<?php

/**
 * TechDivision\ApplicationServer\Api\AbstractService
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
use TechDivision\ApplicationServer\Api\ServiceInterface;
use TechDivision\PersistenceContainer\Application;

/**
 * Abstract service implementation.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractService implements ServiceInterface
{

    /**
     * Primary key field to use for container entity.
     *
     * @var string
     */
    const PRIMARY_KEY = 'id';

    /**
     * The initial context instance containing the system configuration.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * Initializes the service with the initial context instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     * @return void
     */
    public function __construct(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    public function getInitialContext()
    {
        return $this->initialContext;
    }

    public function getSystemConfiguration()
    {
        return $this->getInitialContext()->getSystemConfiguration();
    }

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance($className, array $args = array())
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

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

        // set the node value if available
        if ($value = $configuration->getValue()) {
            $node->value = $value;
        }

        // set members by converting camel case to underscore (necessary for ember.js)
        foreach ($configuration->getAllData() as $member => $value) {
            $node->{strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $member))} = $value;
        }

        // return the normalized node instance
        return $node;
    }
}