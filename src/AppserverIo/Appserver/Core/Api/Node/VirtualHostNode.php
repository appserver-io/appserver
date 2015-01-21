<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\VirtualHostNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Appserver\Core\Api\ExtensionInjectorParameterTrait;

/**
 * DTO to transfer virtual host information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class VirtualHostNode extends AbstractNode
{

    /**
     * The trait for the virtual host environment variables.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\EnvironmentVariablesNodeTrait
     */
    use EnvironmentVariablesNodeTrait;

    /**
     * The trait for the virtual host params.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The trait for the virtual host rewrite maps.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\RewriteMapsNodeTrait
     */
    use RewriteMapsNodeTrait;

    /**
     * The trait for the virtual host rewrites.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\RewritesNodeTrait
     */
    use RewritesNodeTrait;

    /**
     * The trait for the virtual host access.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\AccessesNodeTrait
     */
    use AccessesNodeTrait;

    /**
     * The trait for the virtual host locations.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\LocationsNodeTrait
     */
    use LocationsNodeTrait;

    /**
     * The trait for the virtual host extension injectors.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\ExtensionInjectorParameterTrait
     */
    use ExtensionInjectorParameterTrait;

    /**
     * The trait for the virtual host authentications.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\AuthenticationsNodeTrait
     */
    use AuthenticationsNodeTrait;

    /**
     * The trait for the virtual host analytics.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\AnalyticsNodeTrait
     */
    use AnalyticsNodeTrait;

    /**
     * The virtual host name
     *
     * @var string
     * @AS\Mapping(nodeType="string")
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
}
