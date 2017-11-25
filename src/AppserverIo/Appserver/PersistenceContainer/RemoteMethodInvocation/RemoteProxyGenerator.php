<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation\ProxyGenerator
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
 * @copyright 2017 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation;

use AppserverIo\Appserver\Core\Utilities\FileSystem;
use AppserverIo\Psr\Deployment\DescriptorInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A proxy generator implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2017 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class RemoteProxyGenerator implements ProxyGeneratorInterface
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Inject's the application instance.
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
     * Return's the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Generate's a RMI proxy based on the passe descriptor information and
     * registers it in the naming directory.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $descriptor The descriptor with the proxy data used for generation
     *
     * @return void
     * @link https://github.com/appserver-io/rmi
     */
    public function generate(DescriptorInterface $descriptor)
    {

        // start generating the proxy
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'Proxy' . DIRECTORY_SEPARATOR . 'RemoteProxy.dhtml';
        $content = ob_get_clean();

        // prepare the proxy's filename
        $filename = sprintf('%s/%s', $this->getApplication()->getCacheDir(), $this->generateRemoteProxyFilename($descriptor));

        // query whether or not proxy's directory has to be created
        if (!is_dir($directory = dirname($filename))) {
            FileSystem::createDirectory($directory, 0755, true);
        }

        // write the proxy to the filesystem
        file_put_contents($filename, $content);

        // load the application instance
        $application = $this->getApplication();

        // register the proxy in the naming directory
        $application->getNamingDirectory()
                    ->bind(
                        sprintf('php:global/%s/%s/proxy', $application->getUniqueName(), $descriptor->getName()),
                        sprintf('%sRemoteProxy', $descriptor->getClassName())
                    );
    }

    /**
     * Generates the method signature based on the passed reflection method.
     *
     * @param \ReflectionMethod $reflectionMethod The reflection method to generate the proxy for
     *
     * @return string The method signature as string
     */
    protected function generateMethodSignature(\ReflectionMethod $reflectionMethod)
    {

        // initialize the array for the method params
        $params = array();

        // iterate over the reflection method's parameters
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            // initialize the param
            $param = '';

            // add the type hint, if specified
            if ($typeHint = $reflectionParameter->getClass()) {
                $param .= sprintf('\%s ', $typeHint->getName());
            }

            // add the array type hint, if specified
            if ($reflectionParameter->isArray()) {
                $param .= 'array ';
            }

            // add the by reference symbol, if necessary
            if ($reflectionParameter->isPassedByReference()) {
                $param .= '&';
            }

            // prepare the parameter name
            $param .= sprintf('$%s', $reflectionParameter->getName());

            // query whether or not the parameter has a default value
            if ($reflectionParameter->isDefaultValueAvailable()) {
                // if yes, try to load the type
                $type = gettype($defaultValue = $reflectionParameter->getDefaultValue());

                // qoute it, if it's a string
                switch ($type) {
                    case 'string':
                        $param .= sprintf(' = "%s"', $defaultValue);
                        break;
                    default:
                        $param .= sprintf(' = %s', $defaultValue);

                }
            }

            // append the parameter to the array
            $params[] = $param;
        }

        // implode the paramters and return them as string
        return implode(', ', $params);
    }

    /**
     * Generate the method parameters that'll be passed to the __call method.
     *
     * @param \ReflectionMethod $reflectionMethod The reflection method to generate the parameters for
     *
     * @return string The method parameters as string
     */
    protected function generateMethodParams(\ReflectionMethod $reflectionMethod)
    {

        // initialize the array for the params
        $params = array();

        // assembler the parameters
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $params[] = sprintf('$%s', $reflectionParameter->getName());
        }

        // implode and return them
        return implode(', ', $params);
    }

    /**
     * Generate the implements part of the class definition.
     *
     * @param \ReflectionClass $reflectionClass The reflection class to generate the implements part for
     *
     * @return string The implements part of the class definition
     */
    protected function generateImplements(\ReflectionClass $reflectionClass)
    {

        // load the class interfaces
        $interfaces = $reflectionClass->getInterfaces();

        // initialize the array for the
        $generateInterfaces = array();

        // iterate over the specified interfaces
        foreach ($interfaces as $interfaceName => $interface) {
            // clean them up to avoid doubles
            foreach ($generateInterfaces as $obj) {
                if ($obj->isSubclassOf($interface)) {
                    continue 2;
                }
            }

            // traversable interface can not be implemented directly, so ignore it
            if ($interface->getName() === 'Traversable') {
                continue;
            }

            // append the interface
            $generateInterfaces[sprintf('\\%s', $interfaceName)] = $interface;
        }

        // implode the interfaces and return them as string
        return implode(', ', array_keys($generateInterfaces));
    }

    /**
     * Return's the remote proxy's class name.
     *
     * @param \ReflectionClass $reflectionClass The reflection class to return the remote proxy's class name for
     *
     * @return string The remote proxy's class name
     */
    protected function generateRemoteProxyClassName(\ReflectionClass $reflectionClass)
    {
        return sprintf('%sRemoteProxy', $reflectionClass->getShortName());
    }

    /**
     * Return's the remote proxy's filename.
     *
     * @param \AppserverIo\Psr\Deployment\DescriptorInterface $descriptor The reflection class to return the remote proxy's filename for
     *
     * @return string The remote proxy's filename
     */
    protected function generateRemoteProxyFilename(DescriptorInterface $descriptor)
    {
        return sprintf('%sRemoteProxy.php', str_replace('\\', '/', $descriptor->getClassName()));
    }
}
