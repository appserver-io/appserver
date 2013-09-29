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
     * XPath expression for the container base directory configuration.
     *
     * @var string
     */
    const XPATH_BASE_DIRECTORY = '/appserver/baseDirectory';

    /**
     * The initial context instance containing the system configuration.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The normalizer instance to use.
     *
     * @var \TechDivision\ApplicationServer\Api\NormalizerInterface
     */
    protected $normalizer;

    /**
     * Initializes the service with the initial context instance and the
     * default normalizer instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     * @return void
     */
    public function __construct(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
        $this->setNormalizer($this->newInstance('TechDivision\ApplicationServer\Api\DtoNormalizer', array($this->getInitialContext())));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::getInitialContext()
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::getNormalizer()
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }

    /**
     * Set's the normalizer to use.
     *
     * @param \TechDivision\ApplicationServer\Api\NormalizerInterface $normalizer
     *            The normalizer to use
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::getSystemConfiguration()
     */
    public function getSystemConfiguration()
    {
        return $this->getInitialContext()->getSystemConfiguration();
    }

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Return's the application server's base directory, that is
     * /opt/appserver by default.
     *
     * @return string The application server's base directory
     */
    public function getBaseDirectory()
    {
        return $this->getSystemConfiguration()
            ->getChild(self::XPATH_BASE_DIRECTORY)
            ->getValue();
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
        return $this->getNormalizer()->normalize($configuration);
    }

    /**
     * Creates a new application configuration node with the application data and
     * returns it.
     *
     * @param string $nodeName
     *            The node name to be used
     * @param array $data
     *            The array with the configuration's attributes
     * @param array $children
     *            The array with the configuration's children
     * @param string $value
     *            The configuration's node value
     * @return \TechDivision\ApplicationServer\Configuration The application configuration instance
     */
    public function newConfiguration($nodeName, array $data = array(), array $children = array(), $value = null)
    {
        $configuration = $this->newInstance('\TechDivision\ApplicationServer\Configuration');
        $configuration->setNodeName($nodeName);
        $configuration->setAllData($data);
        $configuration->setChildren($children);
        $configuration->setValue($value);
        return $configuration;
    }
}