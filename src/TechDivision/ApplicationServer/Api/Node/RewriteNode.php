<?php
/**
 * TechDivision\ApplicationServer\Api\Node\RewriteNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

use TechDivision\ApplicationServer\Api\ExtensionInjectorParameterTrait;

/**
 * DTO to transfer module information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class RewriteNode extends AbstractNode
{
    // We have to use this trait to allow for the injection of additional target strings
    use ExtensionInjectorParameterTrait;

    /**
     * The rewrite condition.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $condition;

    /**
     * The rule target.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $target;

    /**
     * The rewrite flat.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $flag;

    /**
     * Returns the rewrite condition.
     *
     * @return string The rewrite condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Returns the rule target.
     *
     * @return string The rule target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Returns the rewrite flag.
     *
     * @return string The rewrite flag
     */
    public function getFlag()
    {
        return $this->flag;
    }
}
