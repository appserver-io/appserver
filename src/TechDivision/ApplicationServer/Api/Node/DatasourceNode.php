<?php

/**
 * TechDivision\ApplicationServer\Api\Node\DatasourceNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a datasource.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class DatasourceNode extends AbstractNode
{

    /**
     * The unique datasource name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The database connection information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DatabaseNode
     * @AS\Mapping(nodeName="database", nodeType="TechDivision\ApplicationServer\Api\Node\DatabaseNode")
     */
    protected $database;

    /**
     * Returns the unique datasource name.
     *
     * @return string The unique datasource name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the database connection information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\DatabaseNode The database connection information
     */
    public function getDatabase()
    {
        return $this->database;
    }
}
