<?php

/**
 * AppserverIo\Appserver\Core\Api\AbstractService
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

use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Lang\NotImplementedException;

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
     * @return \AppserverIo\Configuration\Interfaces\NodeInterface The system configuration
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::getSystemConfiguration()
     */
    public function getSystemConfiguration()
    {
        return $this->getInitialContext()->getSystemConfiguration();
    }

    /**
     * (non-PHPdoc)
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $systemConfiguration The system configuration
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::setSystemConfiguration()
     */
    public function setSystemConfiguration(NodeInterface $systemConfiguration)
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
     * Makes the path an absolute path or returns null if passed path is empty.
     *
     * @param string $path A path to absolute
     *
     * @return string The absolute path
     */
    protected function makePathAbsolute($path = '')
    {
        if (empty($path) === false) {
            return DIRECTORY_SEPARATOR . trim(DirectoryKeys::realpath($path), DIRECTORY_SEPARATOR);
        }
    }

    /**
     * Returns the servers tmp directory, append with the passed directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getTmpDir($relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $this->getSystemConfiguration()->getParam(DirectoryKeys::VAR_TMP) . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Returns the servers deploy directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getDeployDir($relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $this->getSystemConfiguration()->getParam(DirectoryKeys::DEPLOY) . $this->makePathAbsolute($relativePathToAppend)
            )
        );
    }

    /**
     * Returns the servers webapps directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getWebappsDir($relativePathToAppend = '')
    {
        return $this->realpath(
            $this->makePathAbsolute(
                $this->getSystemConfiguration()->getParam(DirectoryKeys::WEBAPPS) . $this->makePathAbsolute($relativePathToAppend)
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
        return strtoupper(substr(PHP_OS, 0, 3));
    }

    /**
     * Returns the servers main configuration directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string
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
     * Returns the servers configuration subdirectory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string
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
        // TODO implement this
        throw new NotImplementedException();
    }

    /**
     * Recursively parses and returns the directories that matches the passed
     * glob pattern.
     *
     * @param string  $pattern The glob pattern used to parse the directories
     * @param integer $flags   The flags passed to the glob function
     *
     * @return array The directories matches the passed glob pattern
     * @link http://php.net/glob
     */
    public function globDir($pattern, $flags = 0)
    {

        // parse the first directory
        $files = glob($pattern, $flags);

        // parse all subdirectories
        foreach (glob(dirname($pattern). DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_BRACE) as $dir) {
            $files = array_merge($files, $this->globDir($dir . DIRECTORY_SEPARATOR . basename($pattern), $flags));
        }

        // return the array with the files matching the glob pattern
        return $files;
    }
}
