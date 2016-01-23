<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SecurityNodeInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for a security DTO implementation.
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */
interface SecurityNodeInterface extends NodeInterface
{

    /**
     * Return's the URL pattern information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\UrlPatternNode The URL pattern
     */
    public function getUrlPattern();

    /**
     * Return's the authentication information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthNode The authentication information
     */
    public function getAuth();

    /**
     * Returns the authentication type class name for the passed shortname.
     *
     * @param string $shortname The shortname of the requested authentication type class name
     *
     * @return string The requested authentication type class name
     * @throws ConfigurationException
     */
    public function mapAuthenticationType($shortname);
}
