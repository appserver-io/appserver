<?php

/**
 * TechDivision\ApplicationServer\Api\Node\AppNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a app.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AppNode extends AbstractNode
{

    /**
     * The containers the app belongs to.
     *
     * @var array
     */
    protected $containers = array();

    /**
     * The unique application name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The application's path.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $webappPath;

    /**
     * Returns the nodes primary key.
     *
     * @return string The nodes primary key
     * @see \TechDivision\ApplicationServer\Api\Node\AbstractNode::getPrimaryKey();
     */
    public function getPrimaryKey()
    {
        return $this->getWebappPath() . DIRECTORY_SEPARATOR . $this->getName();
    }

    /**
     * Set's the application name.
     *
     * @param string $name The unique application name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return's the application name.
     *
     * @return string The unique application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set's the application's path.
     *
     * @param string $name The application's path
     * @return void
     */
    public function setWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Return's the application's path.
     *
     * @return string The application's path
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    public function inContainer($containerUuid)
    {
        return array_key_exists($containerUuid, $this->getContainers());
    }

    public function addContainer(ContainerNode $container)
    {
        $this->containers[$container->getUuid()] = $container;
    }

    public function getContainers()
    {
        return $this->containers;
    }
}