<?php
/**
 * AppserverIo\Appserver\PersistenceContainer\TimedObject
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\EnterpriseBeans\Annotations\MessageDriven;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PostConstruct;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PreDestroy;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Schedule;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Singleton;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Startup;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateful;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Stateless;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Timeout;
use AppserverIo\Psr\EnterpriseBeans\Annotations\EnterpriseBean;
use AppserverIo\Psr\EnterpriseBeans\Annotations\Resource;

/**
 * A wrapper instance for a reflection class.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class TimedObject extends ReflectionClass
{

    /**
     * Creates a new reflection class instance from the passed PHP reflection class.
     *
     * @param \ReflectionClass $reflectionClass     The PHP reflection class to load the data from
     * @param array            $annotationsToIgnore An array with annotations names we want to ignore when loaded
     * @param array            $annotationAliases   An array with annotation aliases used when create annotation instances
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The instance
     */
    public static function fromPhpReflectionClass(\ReflectionClass $reflectionClass, array $annotationsToIgnore = array(), array $annotationAliases = array())
    {

        // initialize the array with the annotations we want to ignore
        $annotationsToIgnore = array_merge(
            $annotationsToIgnore,
            array(
                'author',
                'package',
                'license',
                'copyright',
                'param',
                'return',
                'throws',
                'see',
                'link'
            )
        );

        // initialize the array with the aliases for the enterprise bean annotations
        $annotationAliases = array_merge(
            array(
                MessageDriven::ANNOTATION  => MessageDriven::__getClass(),
                PostConstruct::ANNOTATION  => PostConstruct::__getClass(),
                PreDestroy::ANNOTATION     => PreDestroy::__getClass(),
                Schedule::ANNOTATION       => Schedule::__getClass(),
                Singleton::ANNOTATION      => Singleton::__getClass(),
                Startup::ANNOTATION        => Startup::__getClass(),
                Stateful::ANNOTATION       => Stateful::__getClass(),
                Stateless::ANNOTATION      => Stateless::__getClass(),
                Timeout::ANNOTATION        => Timeout::__getClass(),
                EnterpriseBean::ANNOTATION => EnterpriseBean::__getClass(),
                Resource::ANNOTATION       => Resource::__getClass()
            )
        );

        // create a new timed object instance
        return new TimedObject($reflectionClass->getName(), $annotationsToIgnore, $annotationAliases);
    }
}
