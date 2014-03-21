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
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * TechDivision\ApplicationServer\Api\Node\AbstractRewritesNode
 *
 * Abstract node that serves nodes having a params/param (we extends from AbstractParamsNode) and a
 * rewrites/rewrite child.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
abstract class AbstractRewritesNode extends AbstractParamsNode
{
    /**
     * The virtual host specific rewrite rules.
     *
     * @var array
     * @AS\Mapping(nodeName="rewrites/rewrite", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\RewriteNode")
     */
    protected $rewrites = array();

    /**
     * Will return the rewrites array.
     *
     * @return array
     */
    public function getRewrites()
    {
        return $this->rewrites;
    }

    /**
     * Will return the rewrite node with the specified condition and if nothing could be found we will return false.
     *
     * @param string $condition The condition of the rewrite in question
     *
     * @return \TechDivision\ApplicationServer\Api\Node\RewriteNode|boolean The requested rewrite node
     */
    public function getRewrite($condition)
    {
        // Iterate over all rewrites
        foreach ($this->getRewrites() as $rewriteNode) {

            // If we found one with a matching condition we will return it
            if ($rewriteNode->getCondition() === $condition) {

                return $rewriteNode;
            }
        }

        // Still here? Seems we did not find anything
        return false;
    }

    /**
     * Returns the rewrites as an associative array.
     *
     * @return array The array with the sorted rewrites
     */
    public function getRewritesAsArray()
    {
        // Iterate over the rewrite nodes and sort them into an array
        $rewrites = array();
        foreach ($this->getRewrites() as $rewriteNode) {

            // Restructure to an array
            $rewrites[$rewriteNode->getCondition()] = array(
                'condition' => $rewriteNode->getCondition(),
                'target' => $rewriteNode->getTarget(),
                'flag' => $rewriteNode->getFlag()
            );
        }

        // Return what we got
        return $rewrites;
    }
}
