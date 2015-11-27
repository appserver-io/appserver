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

/**
 * Interface for datasource node implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface DatasourceNodeInterface
{

    /**
     * Returns the unique datasource name.
     *
     * @return string|null The unique datasource name
     */
    public function getName();

    /**
     * Returns the datasource's type.
     *
     * @return string|null The datasource type
     */
    public function getType();

    /**
     * Returns the database connection information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabaseNode The database connection information
     */
    public function getDatabase();

    /**
     * Returns the name of the container which can use this datasource
     *
     * @return string
     */
    public function getContainerName();
}
