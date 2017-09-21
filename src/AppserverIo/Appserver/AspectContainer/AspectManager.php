<?php

/**
 * \AppserverIo\Appserver\AspectContainer\AspectManager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Appserver\AspectContainer;

use AppserverIo\Appserver\AspectContainer\Interfaces\AspectManagerInterface;
use AppserverIo\Appserver\Core\Api\InvalidConfigurationException;
use AppserverIo\Appserver\Core\DgClassLoader;
use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;
use AppserverIo\Doppelgaenger\AspectRegister;
use AppserverIo\Doppelgaenger\Config;
use AppserverIo\Doppelgaenger\Entities\Definitions\Advice;
use AppserverIo\Doppelgaenger\Entities\Definitions\Aspect;
use AppserverIo\Doppelgaenger\Entities\Definitions\Pointcut;
use AppserverIo\Doppelgaenger\Entities\PointcutExpression;
use AppserverIo\Doppelgaenger\Entities\Pointcuts\PointcutFactory;
use AppserverIo\Doppelgaenger\Entities\Pointcuts\PointcutPointcut;
use AppserverIo\Doppelgaenger\Parser\AspectParser;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\MetaobjectProtocol\Aop\Annotations\Aspect as AspectAnnotation;

/**
 * Manager which enables the registration of aspects within a certain application context
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 *
 * @property \AppserverIo\Doppelgaenger\AspectRegister         $aspectRegister The aspect register used for registering the found aspects of this application
 * @property \AppserverIo\Psr\Application\ApplicationInterface $application    The application to manage
 */
class AspectManager implements AspectManagerInterface, ManagerInterface
{

    /**
     * The name of the file which might contain additional pointcuts/advices
     *
     * @var string
     */
    const CONFIG_FILE_GLOB = 'pointcuts';

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
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * The managers unique identifier.
     *
     * @return string The unique identifier
     */
    public function getIdentifier()
    {
        return AspectManagerInterface::IDENTIFIER;
    }

    /**
     * Returns a reflection class instance for the passed class name.
     *
     * @param string $className The class name to return the reflection class instance for
     *
     * @return \AppserverIo\Lang\Reflection\ReflectionClass The reflection instance
     * @see \AppserverIo\Psr\Di\ProviderInterface::getReflectionClass()
     */
    public function getReflectionClass($className)
    {
        return $this->getApplication()->search('ProviderInterface')->getReflectionClass($className);
    }

    /**
     * Returns the absolute path to the web application.
     *
     * @return string The absolute path
     */
    public function getWebappPath()
    {
        return $this->getApplication()->getWebappPath();
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface $application The application instance
     *
     * @return void
     */
    public function initialize(ApplicationInterface $application)
    {

        /** @var \AppserverIo\Appserver\Core\DgClassLoader $dgClassLoader */
        $dgClassLoader = $application->search('DgClassLoader');

        // if we did not get the correct class loader our efforts are for naught
        if (!$dgClassLoader instanceof DgClassLoader) {
            $application->getInitialContext()->getSystemLogger()->warning(
                sprintf(
                    'Application %s uses the aspect manager but does not have access to the required Doppelgaenger class loader, AOP functionality will be omitted.',
                    $application->getName()
                )
            );
            return;
        }

        // register the aspects and tell the class loader it can fill the cache
        $this->registerAspects($application);

        // inject the filled aspect register and create the cache based on it
        $dgClassLoader->injectAspectRegister($this->getAspectRegister());
        $dgClassLoader->createCache();
    }

    /**
     * Inject the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Inject the aspect register
     *
     * @param \AppserverIo\Doppelgaenger\AspectRegister $aspectRegister The aspect register instance
     *
     * @return null
     */
    public function injectAspectRegister(AspectRegister $aspectRegister)
    {
        $this->aspectRegister = $aspectRegister;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     *
     * @throws \Exception
     */
    public function getAttribute($key)
    {
        throw new \Exception(sprintf('%s is not implemented yes', __METHOD__));
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
        // try both sources for pointcuts/aspects, XML and class files
        $this->registerAspectClasses($application);
        $this->registerAspectXml($application);
    }

    /**
     * Registers aspects written within source files which we might encounter
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    protected function registerAspectClasses(ApplicationInterface $application)
    {

        // check directory for PHP files with classes we want to register
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $service */
        $service = $application->newService('AppserverIo\Appserver\Core\Api\DeploymentService');

        // iterate over the directories and try to find aspects
        $aspectDirectories = $service->globDir($this->getWebappPath() . DIRECTORY_SEPARATOR . '{WEB-INF,META-INF,common}' .
            DIRECTORY_SEPARATOR . 'classes', GLOB_BRACE);
        foreach ($aspectDirectories as $aspectDirectory) {
            // iterate all PHP files found in the directory
            foreach ($service->globDir($aspectDirectory . DIRECTORY_SEPARATOR . '*.php') as $phpFile) {
                try {
                    // cut off the META-INF directory and replace OS specific directory separators
                    $relativePathToPhpFile = str_replace(DIRECTORY_SEPARATOR, '\\', str_replace($aspectDirectory, '', $phpFile));

                    // now cut off the .php extension
                    $className = substr($relativePathToPhpFile, 0, -4);

                    // we need a reflection class to read the annotations
                    $reflectionClass = $this->getReflectionClass($className);

                    // if we found an aspect we have to register it using our aspect register class
                    if ($reflectionClass->hasAnnotation(AspectAnnotation::ANNOTATION)) {
                        $parser = new AspectParser($phpFile, new Config());
                        $this->aspectRegister->register(
                            $parser->getDefinition($reflectionClass->getShortName(), false)
                        );
                    }

                // if class can not be reflected continue with next class
                } catch (\Exception $e) {
                    // log an error message
                    $application->getInitialContext()->getSystemLogger()->error($e->__toString());

                    // proceed with the next class
                    continue;
                }
            }
        }
    }

    /**
     * Registers aspects written within source files which we might encounter
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function registerAspectXml(ApplicationInterface $application)
    {

        /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
        $configurationService = $application->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');

        // check if we even have a XMl file to read from
        $xmlPaths = $configurationService->globDir(
            AppEnvironmentHelper::getEnvironmentAwareGlobPattern($this->getWebappPath(), '{WEB-INF,META-INF,common}' . DIRECTORY_SEPARATOR . self::CONFIG_FILE_GLOB, GLOB_BRACE),
            GLOB_BRACE
        );
        foreach ($xmlPaths as $xmlPath) {
            // iterate all XML configuration files we found
            if (is_readable($xmlPath)) {
                // validate the file here, if it is not valid we can skip further steps
                try {
                    $configurationService->validateFile($xmlPath, null, true);
                } catch (InvalidConfigurationException $e) {
                    /** @var \Psr\Log\LoggerInterface $systemLogger */
                    $systemLogger = $this->getApplication()->getInitialContext()->getSystemLogger();
                    $systemLogger->error($e->getMessage());
                    $systemLogger->critical(
                        sprintf(
                            'Pointcuts configuration file %s is invalid, AOP functionality might not work as expected.',
                            $xmlPath
                        )
                    );
                    continue;
                }

                // load the aop config
                $config = new \SimpleXMLElement(file_get_contents($xmlPath));
                $config->registerXPathNamespace('a', 'http://www.appserver.io/appserver');

                // create us an aspect
                // name of the aspect will be the application name
                $aspect = new Aspect();
                $aspect->setName($xmlPath);

                // check if we got some pointcuts
                foreach ($config->xpath('/a:pointcuts/a:pointcut') as $pointcutConfiguration) {
                    // build up the pointcut and add it to the collection
                    $pointcut = new Pointcut();
                    $pointcut->setAspectName($aspect->getName());
                    $pointcut->setName((string)$pointcutConfiguration->{'pointcut-name'});
                    $pointcut->setPointcutExpression(
                        new PointcutExpression((string)$pointcutConfiguration->{'pointcut-pattern'})
                    );
                    $aspect->getPointcuts()->add($pointcut);
                }

                // check if we got some advices
                foreach ($config->xpath('/a:pointcuts/a:advice') as $adviceConfiguration) {
                    // build up the advice and add it to the aspect
                    $advice = new Advice();
                    $advice->setAspectName((string)$adviceConfiguration->{'advice-aspect'});
                    $advice->setName($advice->getAspectName() . '->' . (string)$adviceConfiguration->{'advice-name'});
                    $advice->setCodeHook((string)$adviceConfiguration->{'advice-type'});

                    $pointcutPointcut = $this->generatePointcutPointcut((array) $adviceConfiguration->{'advice-pointcuts'}->{'pointcut-name'}, $aspect);
                    $advice->getPointcuts()->add($pointcutPointcut);

                    // finally add the advice to our aspect (we will also add it without pointcuts of its own)
                    $aspect->getAdvices()->add($advice);
                }

                // if the aspect contains pointcuts or advices it can be used
                if ($aspect->getPointcuts()->count() > 0 || $aspect->getAdvices()->count() > 0) {
                    $this->getAspectRegister()->set($aspect->getName(), $aspect);
                }
            }
        }
    }

    /**
     * Will create a PointcutPointcut instance referencing all concrete pointcuts configured for a certain advice.
     * Needs a list of these pointcuts
     *
     * @param array                                                  $pointcutNames List of names of referenced pointcuts
     * @param \AppserverIo\Doppelgaenger\Entities\Definitions\Aspect $aspect        The aspect to which the advice belongs
     *
     * @return \AppserverIo\Doppelgaenger\Entities\Pointcuts\PointcutPointcut
     */
    protected function generatePointcutPointcut(array $pointcutNames, Aspect $aspect)
    {
        // there might be several pointcuts
        // we have to look them up within the pointcuts we got here and the ones we already have in our register
        $pointcutFactory = new PointcutFactory();
        $referencedPointcuts = array();
        $pointcutExpression = array();
        foreach ($pointcutNames as $pointcutName) {
            $pointcutName = (string) $pointcutName;
            $referenceCount = count($referencedPointcuts);

            // check if we recently parsed the referenced pointcut
            if ($pointcut = $aspect->getPointcuts()->get($pointcutName)) {
                $referencedPointcuts[] = $pointcut;
            } else {
                // or did we already know of it?
                $referencedPointcuts = array_merge($referencedPointcuts, $this->getAspectRegister()->lookupPointcuts($pointcutName));
            }

            // build up the expression string for the PointcutPointcut instance
            if ($referenceCount < count($referencedPointcuts)) {
                $pointcutExpression[] = $pointcutName;
            }
        }

        /** @var \AppserverIo\Doppelgaenger\Entities\Pointcuts\PointcutPointcut $pointcutPointcut */
        $pointcutPointcut = $pointcutFactory->getInstance(
            PointcutPointcut::TYPE . '(' . implode(PointcutPointcut::EXPRESSION_CONNECTOR, $pointcutExpression) . ')'
        );
        $pointcutPointcut->setReferencedPointcuts($referencedPointcuts);

        return $pointcutPointcut;
    }
}
