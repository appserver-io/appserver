<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\AnnotationRegistryNodeInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Interface for all DTOs to transfer a doctrine entity manager custom annotation configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface AnnotationRegistryNodeInterface
{

    /**
     * Array with the directories.
     *
     * @param array $directories The directories
     *
     * @return void
     */
    public function setDirectories(array $directories);

    /**
     * Array with the directories.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DirectoryNode[]
     */
    public function getDirectories();

    /**
     * Returns an array with the directories as string value, each
     * prepended with the passed value.
     *
     * @param string $prepend Prepend to each directory
     *
     * @return The array with the directories as string
     */
    public function getDirectoriesAsArray($prepend = null);

    /**
     * Returns the annotation registry's type.
     *
     * @return string The fannotation registry's type
     */
    public function getType();

    /**
     * Returns the annotation registry's file.
     *
     * @return string The annotation registry's file
     */
    public function getFile();

    /**
     * Returns the annotation registry's namespace.
     *
     * @return string The annotation registry's namespace
     */
    public function getNamespace();
}
