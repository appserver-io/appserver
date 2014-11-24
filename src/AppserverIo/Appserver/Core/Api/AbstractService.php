<?php
/**
 * AppserverIo\Appserver\Core\Api\AbstractService
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;

/**
 * Abstract service implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
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
        $baseDirectory = $this->getSystemConfiguration()
            ->getBaseDirectory()
            ->getNodeValue()
            ->__toString();

        // if a directory has been passed, make it absolute and append it
        if ($directoryToAppend != null) {
            $baseDirectory .= $this->makePathAbsolute($directoryToAppend);
        }

        // return the base directory, with the passed path appended
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
        return $this->realpath($this->makePathAbsolute(DirectoryKeys::TMP . $this->makePathAbsolute($relativePathToAppend)));
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
        return $this->realpath($this->makePathAbsolute(DirectoryKeys::DEPLOY . $this->makePathAbsolute($relativePathToAppend)));
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
        return $this->realpath($this->makePathAbsolute(DirectoryKeys::WEBAPPS . $this->makePathAbsolute($relativePathToAppend)));
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
        return $this->realpath($this->makePathAbsolute(DirectoryKeys::LOG . $this->makePathAbsolute($relativePathToAppend)));
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
        return $this->realpath($this->makePathAbsolute(DirectoryKeys::CONF . $this->makePathAbsolute($relativePathToAppend)));
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
        return $this->realpath($this->makePathAbsolute(DirectoryKeys::CONFD . $this->makePathAbsolute($relativePathToAppend)));
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

    /**
     * Deletes all files and subdirectories from the passed directory.
     *
     * @param \SplFileInfo $dir             The directory to remove
     * @param bool         $alsoRemoveFiles The flag for removing files also
     *
     * @return void
     */
    public function cleanUpDir(\SplFileInfo $dir, $alsoRemoveFiles = true)
    {

        // first check if the directory exists, if not return immediately
        if ($dir->isDir() === false) {
            return;
        }

        // remove old archive from webapps folder recursively
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir->getPathname()),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            // skip . and .. dirs
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                @rmdir($file->getRealPath());
            } elseif ($file->isFile() && $alsoRemoveFiles) {
                unlink($file->getRealPath());
            } else {
                // do nothing, because file should NOT be deleted obviously
            }
        }
    }

    /**
     * Copies a directory recursively.
     *
     * @param string $src The source directory to copy
     * @param string $dst The target directory
     *
     * @return void
     */
    public function copyDir($src, $dst)
    {
        if (is_link($src)) {
            symlink(readlink($src), $dst);
        } elseif (is_dir($src)) {
            if (is_dir($dst) === false) {
                mkdir($dst, 0775, true);
            }
            // copy files recursive
            foreach (scandir($src) as $file) {
                if ($file != '.' && $file != '..') {
                    $this->copyDir("$src/$file", "$dst/$file");
                }
            }

        } elseif (is_file($src)) {
            copy($src, $dst);
        } else {
            // do nothing, we didn't have a directory to copy
        }
    }

    /**
     * Creates the SSL file passed as parameter or nothing if the file already exists.
     *
     * @param \SplFileInfo $certificate The file info about the SSL file to generate
     *
     * @return void
     */
    public function createSslCertificate(\SplFileInfo $certificate)
    {

        // first we've to check if OpenSSL is available
        if (!extension_loaded('openssl')) {
            return;
        }

        // do nothing if the file is already available
        if ($certificate->isFile()) {
            return;
        }

        // prepare the certificate data from our configuration
        $dn = array(
            "countryName" => "DE",
            "stateOrProvinceName" => "Bavaria",
            "localityName" => "Kolbermoor",
            "organizationName" => "appserver.io",
            "organizationalUnitName" => "Development",
            "commonName" => gethostname(),
            "emailAddress" => "info@appserver.io"
        );

        // check the operating system
        switch (strtoupper(PHP_OS)) {

            case 'DARWIN': // on Mac OS X use the system default configuration

                $configargs = array('config' => '/System/Library/OpenSSL/openssl.cnf');
                break;

            case 'WINNT': // on Windows use the system configuration we deliver

                $configargs = array('config' => $this->getBaseDirectory('/php/extras/ssl/openssl.cnf'));
                break;

            default: // on all other use a standard configuration

                $configargs = array(
                    'digest_alg' => 'md5',
                    'x509_extensions' => 'v3_ca',
                    'req_extensions'   => 'v3_req',
                    'private_key_bits' => 2048,
                    'private_key_type' => OPENSSL_KEYTYPE_RSA,
                    'encrypt_key' => false
                );
        }

        // generate a new private (and public) key pair
        $privkey = openssl_pkey_new($configargs);

        // Generate a certificate signing request
        $csr = openssl_csr_new($dn, $privkey, $configargs);

        // create a self-signed cert that is valid for 365 days
        $sscert = openssl_csr_sign($csr, null, $privkey, 365, $configargs);

        // export the cert + pk files
        $certout = '';
        $pkeyout = '';
        openssl_x509_export($sscert, $certout);
        openssl_pkey_export($privkey, $pkeyout, null, $configargs);

        // write the SSL certificate data to the target
        $file = $certificate->openFile('w');
        if (($written = $file->fwrite($certout . $pkeyout)) === false) {
            throw new \Exception(sprintf('Can\'t create SSL certificate %s', $certificate->getPathname()));
        }

        // log a message that the file has been written successfully
        $this->getInitialContext()->getSystemLogger()->info(
            sprintf('Successfully created %s with %d bytes', $certificate->getPathname(), $written)
        );

        // log any errors that occurred here
        while (($e = openssl_error_string()) !== false) {
            $this->getInitialContext()->getSystemLogger()->debug($e);
        }
    }
}
