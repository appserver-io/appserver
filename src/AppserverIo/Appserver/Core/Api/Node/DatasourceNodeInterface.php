<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\DatasourceNodeInterface
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

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for datasource node implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface DatasourceNodeInterface extends NodeInterface
{

    /**
     * Return's the unique datasource name.
     *
     * @return string|null The unique datasource name
     */
    public function getName();

    /**
     * Return's the datasource's type.
     *
     * @return string|null The datasource type
     */
    public function getType();

    /**
     * Return's the database connection information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabaseNode The database connection information
     */
    public function getDatabase();

    /**
     * Set's the name of the container which can use this datasource.
     *
     * @param string|null $containerName The name of the container
     *
     * @return void
     */
    public function setContainerName($containerName);

    /**
     * Return's the name of the container which can use this datasource
     *
     * @return string
     */
    public function getContainerName();
}
