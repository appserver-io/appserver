<?php
/**
 * TechDivision\ApplicationServer\Api\Node\NamespaceNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer the namespace information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class NamespaceNode extends AbstractValueNode
{

    /**
     * Used to tell design-by-contract if this namespace should be omitted for autoloading
     *
     * @var boolean
     * @AS\Mapping(nodeType="boolean")
     */
    protected $omitAutoLoading;

    /**
     * Used to tell design-by-contract if this namespace should be omitted for enforcement
     *
     * @var boolean
     * @AS\Mapping(nodeType="boolean")
     */
    protected $omitEnforcement;

    /**
     * Returns the omitAutoLoading flag.
     *
     * @return boolean The omitAutoLoading flag
     */
    public function omitAutoLoading()
    {
        return $this->omitAutoLoading;
    }

    /**
     * Returns the omitEnforcement flag.
     *
     * @return boolean The omitEnforcement flag
     */
    public function omitEnforcement()
    {
        return $this->omitEnforcement;
    }
}
