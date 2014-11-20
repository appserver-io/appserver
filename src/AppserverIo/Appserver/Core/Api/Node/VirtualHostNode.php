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
    // We use several traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
    use EnvironmentVariablesNodeTrait;
    use ParamsNodeTrait;
    use RewriteMapsNodeTrait;
    use RewritesNodeTrait;
    use AccessesNodeTrait;
    use LocationsNodeTrait;
    use ExtensionInjectorParameterTrait;
    use AuthenticationsNodeTrait;

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
