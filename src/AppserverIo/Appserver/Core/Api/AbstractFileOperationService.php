<?php
/**
 * \AppserverIo\Appserver\Core\Api\AbstractFileOperationService
 *
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
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Appserver\Core\Utilities\FileSystem;

/**
 * Abstract service implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractFileOperationService extends AbstractService
{

    /**
     * Sets the configured user/group settings on the passed file.
     *
     * @param \SplFileInfo $fileInfo The file to set user/group for
     * @param string       $user     The user that has to own the passed file
     * @param string       $group    The group that has to own the passed file
     *
     * @return void
     */
    public function setUserRight(\SplFileInfo $fileInfo, $user = null, $group = null)
    {

        // Get our system configuration as it contains the user and group to set
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // Check for the existence of a user
        if ($user == null) {
            $user = $systemConfiguration->getParam('user');
        }

        // Check for the existence of a group
        if ($group == null) {
            $group = $systemConfiguration->getParam('group');
        }

        // change the owner for the passed file/directory
        FileSystem::chown($fileInfo, $user, $group);

    }

    /**
     * Will set the owner and group on the passed directory recursively.
     *
     * @param \SplFileInfo $targetDir The directory to set the rights for
     * @param string       $user      The user that has to own the passed directory
     * @param string       $group     The group that has to own the passed directory
     *
     * @return void
     */
    public function setUserRights(\SplFileInfo $targetDir, $user = null, $group = null)
    {

        // Get our system configuration as it contains the user and group to set
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // check for the existence of a user
        if ($user == null) {
            $user = $systemConfiguration->getParam('user');
        }

        // check for the existence of a group
        if ($group == null) {
            $group = $systemConfiguration->getParam('group');
        }

        // change the owner for the passed directory
        FileSystem::recursiveChown($targetDir, $user, $group);
    }

    /**
     * Init the umask to use creating files/directories, either with
     * the passed value or the one found in the configuration.
     *
     * @param integer $umask The new umask to set
     *
     * @return void
     * @throws \Exception Is thrown if the umask can not be set
     */
    public function initUmask($umask = null)
    {

        // check if a umask has been passed
        if ($umask == null) {
            $umask = $this->getInitialContext()->getSystemConfiguration()->getParam('umask');
        }

        // initialize the umask
        FileSystem::initUmask($umask);
    }

    /**
     * Creates the passed directory recursively with the umask specified in the system
     * configuration and sets the user permissions.
     *
     * @param \SplFileInfo $directoryToCreate The directory that should be created
     * @param integer      $mode              The mode to create the directory with
     * @param boolean      $recursively       TRUE if the directory has to be created recursively, else FALSE
     * @param string       $user              The user that has to own the passed directory
     * @param string       $group             The group that has to own the passed directory
     * @param integer      $umask             The new umask to set
     *
     * @return void
     * @throws \Exception Is thrown if the directory can't be created
     */
    public function createDirectory(\SplFileInfo $directoryToCreate, $mode = 0775, $recursively = true, $user = null, $group = null, $umask = null)
    {

        // set the umask that is necessary to create the directory
        $this->initUmask($umask);

        // create the directory itself
        FileSystem::createDirectory($directoryToCreate, $mode, $recursively);

        // load the deployment service
        $this->setUserRights($directoryToCreate, $user, $group);
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
        FileSystem::cleanUpDir($dir, $alsoRemoveFiles);
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
        FileSystem::copyDir($src, $dst);
    }
}
