<?php

/**
 * TechDivision\ApplicationServer\Interfaces\ContainerConfiguration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer\Interfaces;

use TechDivision\ApplicationServer\Api\Node\ContainerNode;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <jz@techdivision.com>
 */
interface ApplicationInterface
{

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The connected application
     */
    public function connect();

    /**
     * Returns the application name (that has to be the class namespace,
     * e. g. TechDivision\Example).
     *
     * @return string The application name
     */
    public function getName();

    /**
     * Return's the applications servers base directory, which is
     * /opt/appserver by default.
     *
     * @param string $directoryToAppend Directory to append before returning the base directory
     * @return string The application server's base directory
     */
    public function getBaseDirectory($directoryToAppend = null);

    /**
     * Returns the path to the appserver webapp base directory.
     *
     * @return string The path to the appserver webapp base directory
     */
    public function getAppBase();

    /**
     * Return's the path to the web application.
     *
     * @return string The path to the web application
     */
    public function getWebappPath();

    /**
     * Returns the application servers base directory (the base directory appended
     * with the container's webroot), /opt/appserver/webapps by default.
     *
     * @return string The container's document root directory
     */
    public function getDocumentRoot();

    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     *
     * @param string $className The class name to create the instance of
     * @param array $args The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array());

    /**
     * Returns the application as a node representation.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\AppNode The node representation of the application
     */
    public function newAppNode();
}