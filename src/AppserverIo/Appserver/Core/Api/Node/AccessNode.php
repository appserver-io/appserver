<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * AppserverIo\Appserver\Core\Api\Node\AccessNode
 *
 * Node class which represents the Access node of the configuration
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class AccessNode extends AbstractNode
{
    use ParamsNodeTrait;

    /**
     * The type for the kind of access mode
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Returns the type for the kind of access mode
     *
     * @return string The type for the kind of access mode
     */
    public function getType()
    {
        return $this->type;
    }
}
