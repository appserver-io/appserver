<?php
/**
 * TechDivision\ApplicationServer\Api\AbstractService
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

namespace TechDivision\ApplicationServer\Api;

use TechDivision\Configuration\Interfaces\NodeInterface;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use TechDivision\PersistenceContainer\Application;

/**
 * Abstract service implementation.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractService implements ServiceInterface
{

    /**
     * The node type to normalize to.
     *
     * @var string
     */
    const NODE_TYPE = 'TechDivision\ApplicationServer\Api\Node\AppserverNode';

    /**
     * The initial context instance containing the system configuration.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The normalizer instance to use.
     *
     * @var \TechDivision\ApplicationServer\Api\NormalizerInterface
     */
    protected $normalizer;

    /**
     * The initialized base directory node.
     *
     * @var \TechDivision\Configuration\Interfaces\NodeInterface;
     */
    protected $node;

    /**
     * Initializes the service with the initial context instance and the
     * default normalizer instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     */
    public function __construct(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * (non-PHPdoc)
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial Context
     * @see ServiceInterface::getInitialContext()
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * (non-PHPdoc)
     *
     * @return \TechDivision\Configuration\Interfaces\NodeInterface The system configuration
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::getSystemConfiguration()
     */
    public function getSystemConfiguration()
    {
        return $this->getInitialContext()->getSystemConfiguration();
    }

    /**
     * (non-PHPdoc)
     *
     * @param \TechDivision\Configuration\Interfaces\NodeInterface $systemConfiguration The system configuration
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::setSystemConfiguration()
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
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
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
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
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
        $baseDirectory = $this->getSystemConfiguration()
            ->getBaseDirectory()
            ->getNodeValue()
            ->__toString();

        if ($directoryToAppend != null) {
            $baseDirectory .= $directoryToAppend;
        }

        return $baseDirectory;
    }

    /**
     * Return's the directory structure to be created at first start.
     *
     * @return array The directory structure to be created if necessary
     */
    public function getDirectories()
    {
        return DirectoryKeys::getDirectories();
    }

    /**
     * Returns the servers tmp directory, append with the passed directory.
     *
     * @param string|null The directory to append
     *
     * @return string
     */
    public function getTmpDir($directoryToAppend = null)
    {
        return $this->realpath(DirectoryKeys::TMP . $directoryToAppend);
    }

    /**
     * Returns the servers deploy directory
     *
     * @return string
     */
    public function getDeployDir()
    {
        return $this->realpath(DirectoryKeys::DEPLOY);
    }

    /**
     * Returns the servers webapps directory
     *
     * @return string
     */
    public function getWebappsDir()
    {
        return $this->realpath(DirectoryKeys::WEBAPPS);
    }

    /**
     * Returns the servers log directory
     *
     * @return string
     */
    public function getLogDir()
    {
        return $this->realpath(DirectoryKeys::LOG);
    }

    /**
     * Returns the absolute path to the passed directory, also
     * working on Windows.
     *
     * @param string $relativeDirectory The relativ path of the directory to return the absolute path for
     *
     * @return string The absolute path of the passed directory
     */
    public function realpath($relativeDirectory)
    {
        return $this->getBaseDirectory(DIRECTORY_SEPARATOR . ltrim(DirectoryKeys::realpath($relativeDirectory), DIRECTORY_SEPARATOR));
    }

    /**
     * Persists the system configuration.
     *
     * @param \TechDivision\Configuration\Interfaces\NodeInterface $node A node to persist
     *
     * @return void
     */
    public function persist(NodeInterface $node)
    {
        // implement this
    }

    /**
     * Sets the configured user/group settings on the passed file.
     *
     * @param \SplFileInfo $fileInfo The file to set user/group for
     *
     * @return void
     */
    public function setUserRight(\SplFileInfo $fileInfo)
    {

        // don't do anything under windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        // Get our system configuration as it contains the user and group to set
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // Check for the existence of a user
        $user = $systemConfiguration->getParam('user');
        if (!empty($user)) {
            chown($fileInfo, $user);
        }

        // Check for the existence of a group
        $group = $systemConfiguration->getParam('group');
        if (!empty($group)) {
            chgrp($fileInfo, $group);
        }
    }

    /**
     * Will set the owner and group on the passed directory.
     *
     * @param \SplFileInfo $targetDir The directory to set the rights for
     *
     * @return void
     */
    public function setUserRights(\SplFileInfo $targetDir)
    {
        // we don't do anything under Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        // we don't have a directory to change the user/group permissions for
        if ($targetDir->isDir() === false) {
            return;
        }

        // Get our system configuration as it contains the user and group to set
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // As we might have several rootPaths we have to create several RecursiveDirectoryIterators.
        $directoryIterator = new \RecursiveDirectoryIterator(
            $targetDir,
            \RecursiveIteratorIterator::SELF_FIRST
        );

        // We got them all, now append them onto a new RecursiveIteratorIterator and return it.
        $recursiveIterator = new \AppendIterator();
            // Append the directory iterator
            $recursiveIterator->append(
                new \RecursiveIteratorIterator(
                    $directoryIterator,
                    \RecursiveIteratorIterator::SELF_FIRST,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD
                )
            );

        // Check for the existence of a user
        $user = $systemConfiguration->getParam('user');
        if (!empty($user)) {

            // Change the rights of everything within the defined dirs
            foreach ($recursiveIterator as $file) {
                chown($file, $user);
            }
        }

        // Check for the existence of a group
        $group = $systemConfiguration->getParam('group');
        if (!empty($group)) {

            // Change the rights of everything within the defined dirs
            foreach ($recursiveIterator as $file) {
                chgrp($file, $group);
            }
        }
    }

    /**
     * Init the umask to use creating files/directories.
     *
     * @return void
     * @throws \Exception Is thrown if the umask can not be set
     */
    public function initUmask()
    {

        // don't do anything under Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }

        // set the configured umask to use
        umask($newUmask = $this->getInitialContext()->getSystemConfiguration()->getParam('umask'));

        // check if we have successfull set the umask
        if (umask() != $newUmask) { // check if set, throw an exception if not
            throw new \Exception(sprintf('Can\'t set configured umask \'%s\' found \'%\' instead', $newUmask, umask()));
        }
    }

    /**
     * Creates the passed directory with the umask specified in the system
     * configuration and sets the user permissions.
     *
     * @param \SplFileInfo $directoryToCreate The directory that should be created
     *
     * @return void
     * @throws \Exception Is thrown if the directory can't be created
     */
    public function createDirectory(\SplFileInfo $directoryToCreate)
    {

        // set the umask that is necessary to create the directory
        $this->initUmask();

        // we don't have a directory to change the user/group permissions for
        if ($directoryToCreate->isDir() === false) {

            // create the directory if necessary
            if (mkdir($directoryToCreate) === false) {
                throw new \Exception(sprintf('Directory %s can\'t be created', $directoryToCreate));
            }
        }

        // load the deployment service
        $this->setUserRights($directoryToCreate);
    }
}
