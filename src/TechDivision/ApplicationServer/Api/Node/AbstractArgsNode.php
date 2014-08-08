<?php

/**
 * TechDivision\ApplicationServer\Api\Node\AbstractArgsNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * Abstract node that serves nodes having a args/arg child.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractArgsNode extends AbstractNode
{

    /**
     * The script args to use.
     *
     * @var array
     * @AS\Mapping(nodeName="args/arg", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ArgNode")
     */
    protected $args = array();

    /**
     * Attaches the passed arg to the list.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\ArgNode $arg The arg to attach
     *
     * @return void
     */
    public function attachArg(ArgNode $arg)
    {
        $this->args[] = $arg;
    }

    /**
     * Array with the args to use.
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Returns the arg with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the arg to be returned
     *
     * @return mixed The requested arg casted to the specified type
     */
    public function getArg($name)
    {
        $args = $this->getArgsAsArray();
        if (array_key_exists($name, $args)) {
            return $args[$name];
        }
    }

    /**
     * Returns the args casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted args
     */
    public function getArgsAsArray()
    {
        $args = array();
        foreach ($this->getArgs() as $arg) {
            $args[$arg->getName()] = $arg->castToType();
        }
        return $args;
    }
}
