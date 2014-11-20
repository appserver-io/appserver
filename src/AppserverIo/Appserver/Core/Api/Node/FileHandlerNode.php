<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\FileHandlerNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer file handler information.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class FileHandlerNode extends AbstractNode
{
    // We use several traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
    use ParamsNodeTrait;

    /**
     * The file handler name
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The file extension
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $extension;

    /**
     * Returns the file handler's name
     *
     * @return string The file handler's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return's the file extension
     *
     * @return string The file extension
     */
    public function getExtension()
    {
        return $this->extension;
    }
}
