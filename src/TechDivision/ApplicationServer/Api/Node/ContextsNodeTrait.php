<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ContextsNodeTrait
 *
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
 * @author     Tim Wagner <tw@techdivision.com>
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 *
 * Abstract node that serves a hosts context nodes.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
trait ContextsNodeTrait
{

    /**
     * The servers context configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="contexts/context", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ContextNode")
     */
    protected $contexts = array();

    /**
     * Returns the servers context configuration.
     *
     * @return array servers context configuration
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * Returns the context with the passed name.
     *
     * @param string $name The name of the requested context
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContextNode|null The requested context node
     */
    public function getContext($name)
    {
        foreach ($this->getContexts() as $context) {
            if ($context->getName() === $name) {
                return $context;
            }
        }
    }
}
