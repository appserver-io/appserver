<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\VirtualHostNode
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
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;
use AppserverIo\Description\Api\Node\ParamsNodeTrait;
use AppserverIo\Appserver\Core\Api\ExtensionInjectorParameterTrait;

/**
 * DTO to transfer virtual host information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class VirtualHostNode extends AbstractNode implements VirtualHostNodeInterface
{

    /**
     * The trait for the virtual host environment variables.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\EnvironmentVariablesNodeTrait
     */
    use EnvironmentVariablesNodeTrait;

    /**
     * The trait for the headers.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\HeadersNodeTrait
     */
    use HeadersNodeTrait;

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Description\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The trait for the virtual host rewrite maps.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\RewriteMapsNodeTrait
     */
    use RewriteMapsNodeTrait;

    /**
     * The trait for the virtual host rewrites.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\RewritesNodeTrait
     */
    use RewritesNodeTrait;

    /**
     * The trait for the virtual host access.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AccessesNodeTrait
     */
    use AccessesNodeTrait;

    /**
     * The trait for the virtual host locations.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\LocationsNodeTrait
     */
    use LocationsNodeTrait;

    /**
     * The trait for the virtual host extension injectors.
     *
     * @var \AppserverIo\Appserver\Core\Api\ExtensionInjectorParameterTrait
     */
    use ExtensionInjectorParameterTrait;

    /**
     * The trait for the virtual host authentications.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthenticationsNodeTrait
     */
    use AuthenticationsNodeTrait;

    /**
     * The trait for the virtual host analytics.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AnalyticsNodeTrait
     */
    use AnalyticsNodeTrait;

    /**
     * The virtual host name
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * Returns the virtual hosts name
     *
     * @return string The server's type
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Converts the virtual host node into an associative array
     * and returns it.
     *
     * @return array The array with the virtual host node data
     */
    public function toArray()
    {
        return array(
            'name' => $this->name
        );
    }
}
