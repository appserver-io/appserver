<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\DynamicPrincipal
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Lang\String;
use AppserverIo\Psr\Security\PrincipalInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

/**
 * This is a dymaical principal implementation that can be used to
 * for login scenarios where the principal data is very volatile.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DynamicPrincipal extends SimplePrincipal
{

    /**
     * The array that contains the dynmic values.
     *
     * @var array
     */
    protected $values = array();

    /**
     * Return's the value with the passed name or NULL if the
     * value is not available.
     *
     * @param string $name The name of the value to return
     *
     * @return mixed|null The requested value
     */
    public function getValue($name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }

    /**
     * I triggered when invoking inaccessible methods in an object context.
     *
     * @param string $name      The name of the method being called
     * @param array  $arguments An enumerated array containing the parameters passed to the method
     *
     * @return mixed Depends on the a getter/setter has been invoked
     * @throws \Exception Is thrown if the method with the passed name is not yet implemented
     * @link https://www.php.net/manual/en/language.oop5.overloading.php#object.call
     */
    public function __call($name, array $arguments)
    {

        // query whether or not we've a getter or setter
        $methodName = substr($name, 0, 3);

        // extract the member name
        $memberName = lcfirst(substr($name, 3));

        // do something, depending on the method
        switch ($methodName) {
            case 'set':
                $this->values[$memberName] = reset($arguments);
                break;
            case 'get':
                return isset($this->values[$memberName]) ? $this->values[$memberName] : null;
                break;
            case 'has':
                return isset($this->values[$memberName]);
                break;
            default:
                throw new \Exception(sprintf('Method %s has not yet been implemented', $name));
        }
    }
}
