<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ErrorPageNodeInterface
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
 * The interface for error page configuration DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ErrorPageNodeInterface
{

    /**
     * The default error code pattern, matches all 400 + 500 error codes.
     *
     * @var string
     */
    const DEFAULT_ERROR_CODE_PATTERN = '[45]*';

    /**
     * The default error location.
     *
     * @var string
     */
    const DEFAULT_ERROR_LOCATION = '/resources/templates/www/dhtml/error.dhtml';

    /**
     * Return's the HTTP response code pattern the error page is defined for.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The HTTP response code pattern
     */
    public function getErrorCodePattern();

    /**
     * Return's the location to redirect to.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The location
     */
    public function getErrorLocation();
}
