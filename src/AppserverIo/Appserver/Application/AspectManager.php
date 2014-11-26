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
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Application;

use AppserverIo\Appserver\Application\Interfaces\AspectInterface;
use AppserverIo\Doppelgaenger\AspectRegister;
use AppserverIo\Doppelgaenger\Config;
use AppserverIo\Doppelgaenger\Entities\Annotations\Aspect;
use AppserverIo\Doppelgaenger\Parser\ClassParser;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Application\ManagerInterface;

/**
 * AppserverIo\Appserver\Application\AspectManager
 *
 * Manager which enables the registration of aspects within a certain application context
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */
class AspectManager implements AspectInterface, ManagerInterface
{

    /**
     * The unique identifier to be registered in the application context.
     *
     * @var string
     */
    const IDENTIFIER = 'AspectManager';

    /**
     * The aspect register used for registering the found aspects of this application
     *
     * @var \AppserverIo\Doppelgaenger\AspectRegister $aspectRegister
     */
    protected $aspectRegister;

    /**
     * Getter for the $aspectRegister property
     *
     * @return \AppserverIo\Doppelgaenger\AspectRegister The aspect register
     */
    public function getAspectRegister()
    {
        return $this->aspectRegister;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return array<\AppserverIo\Doppelgaenger\Entities\Definitions\Aspect> The aspects found for the given key
     */
    public function getAttribute($key)
    {
        return $this->aspectRegister->lookupAspects($key);
    }

    /**
     * The managers unique identifier.
     *
     * @return string The unique identifier
     */
    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function initialize(ApplicationInterface $application)
    {
        $this->aspectRegister = new AspectRegister();
        $this->registerAspects($application);
    }

    /**
     * Inject the aspect register
     *
     * @param \AppserverIo\Doppelgaenger\AspectRegister $aspectRegister
     *
     * @return null
     */
    public function injectAspectRegister(AspectRegister $aspectRegister)
    {
        $this->aspectRegister = $aspectRegister;
    }

    /**
     * Registers the message beans at startup.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    protected function registerAspects(ApplicationInterface $application)
    {

        // build up META-INF directory var
        $metaInfDir = $this->getWebappPath() . DIRECTORY_SEPARATOR .'META-INF';

        // check if we've found a valid directory
        if (is_dir($metaInfDir) === false) {
            return;
        }

        // check meta-inf classes or any other sub folder to pre init aspects
        $recursiveIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($metaInfDir));
        $phpFiles = new \RegexIterator($recursiveIterator, '/^(.+)\.php$/i');

        // iterate all php files
        foreach ($phpFiles as $phpFile) {

            try {

                // cut off the META-INF directory and replace OS specific directory separators
                $relativePathToPhpFile = str_replace(DIRECTORY_SEPARATOR, '\\', str_replace($metaInfDir, '', $phpFile));

                // now cut off the first directory, that'll be '/classes' by default
                $pregResult = preg_replace('%^(\\\\*)[^\\\\]+%', '', $relativePathToPhpFile);
                $className = substr($pregResult, 0, -4);

                // we need a reflection class to read the annotations
                $reflectionClass = $this->newReflectionClass($className);

                // if we found an aspect we have to register it using our aspect register class
                if ($reflectionClass->hasAnnotation(Aspect::ANNOTATION)) { // instanciate the bean
                    $this->getApplication()->search($reflectionClass->getShortName(), array(null, array($application)));

                    $parser = new ClassParser($phpFile, new Config());
                    $this->aspectRegister->register($parser->getDefinition($reflectionClass->getShortName(), false));
                }

            } catch (\Exception $e) { // if class can not be reflected continue with next class

                // log an error message
                $application->getInitialContext()->getSystemLogger()->error($e->__toString());

                // proceed with the next class
                continue;
            }
        }
    }
}
