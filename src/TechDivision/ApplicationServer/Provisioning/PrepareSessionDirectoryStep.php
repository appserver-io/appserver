<?php

/**
 * TechDivision\ApplicationServer\Provisioning\PrepareSessionDirectoryStep
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Provisioning;

/**
 * An step implementation that creates the applications default session storage directory.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class PrepareSessionDirectoryStep extends AbstractStep
{

    /**
     * Executes the functionality for this step, in this case the creation of the
     * session directory for the application.
     *
     * @return void
     * @throws \Exception Is thrown if the script can't be executed
     * @see \TechDivision\ApplicationServer\Provisioning\Step::execute()
     */
    public function execute()
    {

        // create the directory we want to store the sessions in
        $sessionSavePath = new \SplFileInfo($this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'sessions');

        // we don't have a directory to change the user/group permissions for
        if ($sessionSavePath->isDir() === false) {

            // set the umask that is necessary to create the directory
            $this->getService()->initUmask();

            // create the directory if necessary
            if (mkdir($sessionSavePath) === false) {
                throw new \Exception(sprintf('Directory %s to store sessions can\'t be created', $sessionSavePath));
            }

            // set the correct user permissions
            $this->getService()->setUserRights($sessionSavePath);
        }
    }
}
