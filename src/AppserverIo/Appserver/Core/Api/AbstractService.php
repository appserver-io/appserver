<?php

/**
 * \AppserverIo\Appserver\Core\Api\AbstractService
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Properties\Properties;
use AppserverIo\Lang\NotImplementedException;
use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Utilities\FileKeys;
use AppserverIo\Appserver\Core\Utilities\FileSystem;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface;
use AppserverIo\Appserver\Core\Utilities\SystemPropertyKeys;

/**
 * Abstract service implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
abstract class AbstractService implements ServiceInterface
{

    /**
     * The node type to normalize to.
     *
     * @var string
     */
    const NODE_TYPE = 'AppserverIo\Appserver\Core\Api\Node\AppserverNode';

    /**
     * The initial context instance containing the system configuration.
     *
     * @var \AppserverIo\Appserver\Core\InitialContext
     */
    protected $initialContext;

    /**
     * The normalizer instance to use.
     *
     * @var \AppserverIo\Appserver\Core\Api\NormalizerInterface
     */
    protected $normalizer;

    /**
     * The initialized base directory node.
     *
     * @var \AppserverIo\Configuration\Interfaces\NodeInterface;
     */
    protected $node;

    /**
     * Initializes the service with the initial context instance and the
     * default normalizer instance.
     *
     * @param \AppserverIo\Appserver\Core\InitialContext $initialContext The initial context instance
     */
    public function __construct(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * (non-PHPdoc)
     *
     * @return \AppserverIo\Appserver\Core\InitialContext The initial Context
     * @see ServiceInterface::getInitialContext()
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * (non-PHPdoc)
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface The system configuration
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::getSystemConfiguration()
     */
    public function getSystemConfiguration()
    {
        return $this->getInitialContext()->getSystemConfiguration();
    }

    /**
     * (non-PHPdoc)
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration The system configuration
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::setSystemConfiguration()
     */
    public function setSystemConfiguration(SystemConfigurationInterface $systemConfiguration)
    {
        $this->getInitialContext()->setSystemConfiguration($systemConfiguration);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see \AppserverIo\Appserver\Core\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface The service instance
     * @see \AppserverIo\Appserver\Core\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the application servers base directory.
     *
     * @param string|null $directoryToAppend Append this directory to the base directory before returning it
     *
     * @return string The base directory
     */
    public function getBaseDirectory($directoryToAppend = null)
    {

        // load the base directory from the system configuration
        $baseDirectory = $this->getSystemConfiguration()->getBaseDirectory();

        // if a directory has been passed, make it absolute and append it
        if ($directoryToAppend != null) {
            $baseDirectory .= $this->makePathAbsolute($directoryToAppend);
        }

        // return the base directory, with the passed path appended
        return $baseDirectory;
    }

    /**
     * Returns the directory structure to be created at first start.
     *
     * @return array The directory structure to be created if necessary
     */
    public function getDirectories()
    {

        // initialize the array with the directories
        $directories = array();

        // iterate over the directory keys and read the configuration values
        foreach (DirectoryKeys::getServerDirectoryKeys() as $directoryKey) {
            $directories[$directoryKey] = $this->getSystemConfiguration()->getParam($directoryKey);
        }

        // return the array with the directories
        return $directories;
    }

    /**
     * Returns the files to be created at first start.
     *
     * @return array The files to be created if necessary
     */
    public function getFiles()
    {

        // initialize the array with the files
        $files = array();

        // iterate over the file keys and read the configuration values
        foreach (FileKeys::getServerFileKeys() as $fileKey) {
            $files[$fileKey] = $this->getSystemConfiguration()->getParam($fileKey);
        }

        // return the array with the files
        return $files;
    }

    /**
     * Makes the path an absolute path or returns null if passed path is empty.
     *
     * @param string $path A path to absolute
     *
     * @return string The absolute path
     */
    public function makePathAbsolute($path = '')
    {
        if (empty($path) === false) {
            return DIRECTORY_SEPARATOR . trim(DirectoryKeys::realpath($path), DIRECTORY_SEPARATOR);
        }
    }

    /**
     * Returns the servers tmp directory, append with the passed directory.
     *
     * @param ContainerNodeInterface $containerNode        The container to return the temporary directory for
     * @param string                 $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getTmpDir(ContainerNodeInterface $containerNode, $relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $containerNode->getHost()->getTmpBase() . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Returns the servers deploy directory.
     *
     * @param ContainerNodeInterface $containerNode        The container to return the deployment directory for
     * @param string                 $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getDeployDir(ContainerNodeInterface $containerNode, $relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $containerNode->getHost()->getDeployBase() . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Returns the servers webapps directory.
     *
     * @param ContainerNodeInterface $containerNode        The container to return the temporary directory for
     * @param string                 $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getWebappsDir(ContainerNodeInterface $containerNode, $relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $containerNode->getHost()->getAppBase() . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Returns the servers log directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getLogDir($relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $this->getSystemConfiguration()->getParam(DirectoryKeys::VAR_LOG) . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Will return a three character OS identifier e.g. WIN or LIN
     *
     * @return string
     */
    public function getOsIdentifier()
    {
        return FileSystem::getOsIdentifier();
    }

    /**
     * Return's the system's temporary directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string The system's temporary directory
     */
    public function getSystemTmpDir($relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $this->getSystemConfiguration()->getParam(DirectoryKeys::TMP) . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Return's the server's base configuration directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string The server's base configuration directory
     */
    public function getEtcDir($relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $this->getSystemConfiguration()->getParam(DirectoryKeys::ETC) . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Return's the server's main configuration directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string The server's main configuration directory
     */
    public function getConfDir($relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $this->getSystemConfiguration()->getParam(DirectoryKeys::ETC_APPSERVER) . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Return's the server's configuration subdirectory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string The server's configuration subdirectory
     */
    public function getConfdDir($relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $this->getSystemConfiguration()->getParam(DirectoryKeys::ETC_APPSERVER_CONFD) . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Returns the absolute path to the passed directory, also
     * working on Windows.
     *
     * @param string $relativePathToAppend The relativ path to return the absolute path for
     *
     * @return string The absolute path of the passed directory
     */
    public function realpath($relativePathToAppend)
    {
        return $this->getBaseDirectory($this->makePathAbsolute($relativePathToAppend));
    }

    /**
     * Persists the system configuration.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $node A node to persist
     *
     * @return void
     *
     * @throws \AppserverIo\Lang\NotImplementedException Upon call as it did not get implemented yet!
     */
    public function persist(NodeInterface $node)
    {
        throw new NotImplementedException();
    }

    /**
     * Parses and returns the directories and files that matches
     * the passed glob pattern in a recursive way (if wanted).
     *
     * @param string  $pattern   The glob pattern used to parse the directories
     * @param integer $flags     The flags passed to the glob function
     * @param boolean $recursive Whether or not to parse directories recursively
     *
     * @return array The directories matches the passed glob pattern
     * @link http://php.net/glob
     */
    public function globDir($pattern, $flags = 0, $recursive = true)
    {
        return FileSystem::globDir($pattern, $flags, $recursive);
    }

    /**
     * Returns the real server signature depending on the installed
     * appserver version and the PHP version we're running on, for
     * example:
     *
     * appserver/1.0.1-45 (darwin) PHP/5.5.21
     *
     * @return string The server signature
     */
    public function getServerSignature()
    {

        // try to load the OS identifier
        list($os, ) = sscanf(strtolower(php_uname('s')), '%s %s');

        // check if we've a file with the actual version number
        if (file_exists($filename = $this->getConfDir('/.release-version'))) {
            $version = file_get_contents($filename);
        } else {
            $version = 'dev-' . gethostname();
        }

        // prepare and return the server signature
        return sprintf('appserver/%s (%s) PHP/%s', $version, $os, PHP_VERSION);
    }

    /**
     * Returns the system proprties. If a container node has been passed,
     * the container properties will also be appended.
     *
     * @param ContainerNodeInterface|null $containerNode The container to return the system properties for
     *
     * @return \AppserverIo\Properties\Properties The system properties
     */
    public function getSystemProperties(ContainerNodeInterface $containerNode = null)
    {

        // initialize the properties
        $properties = Properties::create();

        // append the system properties
        $properties->add(SystemPropertyKeys::BASE, $this->getBaseDirectory());
        $properties->add(SystemPropertyKeys::VAR_LOG, $this->getLogDir());
        $properties->add(SystemPropertyKeys::ETC, $this->getEtcDir());
        $properties->add(SystemPropertyKeys::ETC_APPSERVER, $this->getConfDir());
        $properties->add(SystemPropertyKeys::ETC_APPSERVER_CONFD, $this->getConfdDir());

        // append the declared system propertie
        /** @var \AppserverIo\Appserver\Core\Api\Node\SystemPropertyNode $systemProperty */
        foreach ($this->getSystemConfiguration()->getSystemProperties() as $systemProperty) {
            $properties->add($systemProperty->getName(), $systemProperty->castToType());
        }

        // query whether or not a container node has been passed
        if ($containerNode != null) {
            // append the container specific properties
            $properties->add(SystemPropertyKeys::TMP, $this->getTmpDir($containerNode));
            $properties->add(SystemPropertyKeys::CONTAINER_NAME, $containerNode->getName());
            $properties->add(SystemPropertyKeys::WEBAPPS, $this->getWebappsDir($containerNode));

            // append the host specific system properties
            if ($host = $containerNode->getHost()) {
                $properties->add(SystemPropertyKeys::HOST_APP_BASE, $host->getAppBase());
                $properties->add(SystemPropertyKeys::HOST_TMP_BASE, $host->getTmpBase());
                $properties->add(SystemPropertyKeys::HOST_DEPLOY_BASE, $host->getDeployBase());
            }
        }

        // return the properties
        return $properties;
    }
}
