<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\WebResourceCollectionNodeInterface
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
 * Interface for a web resource collection DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface WebResourceCollectionNodeInterface extends NodeInterface
{

    /**
     * Return's the web resource name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\WebResourceNameNode The web resource name information
     */
    public function getWebResourceName();

    /**
     * Return's the description information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The description information
     */
    public function getDescription();

    /**
     * Return's the URL pattern information.
     *
     * @return array The URL pattern information
     */
    public function getUrlPatterns();

    /**
     * Return's the HTTP method information.
     *
     * @return array The HTTP method information
     */
    public function getHttpMethods();

    /**
     * Return's the HTTP method omission information.
     *
     * @return array The HTTP method omission information
     */
    public function getHttpMethodOmissions();

    /**
     * Returns the URL patterns as an associative array
     *
     * @return array The array with the sorted URL patterns
     */
    public function getUrlPatternsAsArray();

    /**
     * Returns the HTTP methods as an associative array
     *
     * @return array The array with the HTTP methods
     */
    public function getHttpMethodsAsArray();

    /**
     * Returns the HTTP method omissions as an associative array
     *
     * @return array The array with the HTTP method omissions
     */
    public function getHttpMethodOmissionsAsArray();
}
