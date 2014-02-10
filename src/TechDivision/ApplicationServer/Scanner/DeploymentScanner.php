<?php
/**
 * TechDivision\ApplicationServer\Scanner\DeploymentScanner
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Scanner;

use TechDivision\ApplicationServer\Interfaces\ExtractorInterface;
use TechDivision\ApplicationServer\AbstractContextThread;

/**
 * This is a monitor that watches the deployment directory and restarts
 * the appserver by using the sbin/appserverctl script.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Scanner
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DeploymentScanner extends AbstractContextThread
{
    
    /**
     * The API service used to load the deployment directory.
     * 
     * @var \TechDivision\ApplicationServer\Api\ContainerService
     */
    protected $service;
    
    /**
     * Returns The API service, e. g. to load the deployment directory.
     * 
     * @return \TechDivision\ApplicationServer\Api\ContainerService The API service instance
     */
    public function getService()
    {
        return $this->service;
    }
    
    /**
     * The system logger to use.
     * 
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    public function getSystemLogger()
    {
        return $this->getInitialContext()->getSystemLogger();
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
     * Returns the path to the deployment directory
     * 
     * @return \SplFileInfo The deployment directory
     */
    public function getDirectory()
    {
        return new \SplFileInfo($this->getService()->getDeployDir());
    }
    
    /**
     * Initalizes the scanner with the necessary service instance.
     * 
     * @return void
     * @see \TechDivision\ApplicationServer\AbstractThread::init()
     */
    public function init()
    {
        $this->service = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
    }
    
    /**
     * Start's the deployment scanner that restarts the server
     * when a PHAR should be deployed or undeployed.
     * 
     * @return void
     * @see \TechDivision\ApplicationServer\AbstractThread::main()
     */
    public function main()
    {
        
        // load the deployment directory
        $directory = $this->getDirectory();
    
        // log the configured deployment directory
        $this->getSystemLogger()->debug(
            sprintf(
                "Start watching deployment directory %s",
                $directory
            )
        );
        
        // load the initial hash value of the deployment directory
        $oldHash = $this->getDirectoryHash($directory);
        
        while (true) { // watch the deployment directory
            
            // load the actual hash value for the deployment directory
            $newHash = $this->getDirectoryHash($directory);
            
            // log the found directory hash value
            $this->getSystemLogger()->debug(
                sprintf(
                    "Comparing directory hash %s (previous) : %s (actual)",
                    $oldHash,
                    $newHash
                )
            );

            // compare the hash values, if not equal restart the appserver
            if ($oldHash !== $newHash) {

                // log that changes have been found
                $this->getSystemLogger()->debug(
                    sprintf(
                        "Found changes in deplyoment directory",
                        $directory
                    )
                );

                // log the UNIX timestamp of the last successfull deployment
                $lastSuccessfullDeployment = $this->getLastSuccessfullyDeployment($directory);
                
                // restart the appserver
                $this->restart();
                
                // wait until deployment has been finished
                while ($lastSuccessfullDeployment == $this->getLastSuccessfullyDeployment($directory)) {
                    sleep(1);
                }
                
                // set the directory new hash value after successfull deployment
                $oldHash = $this->getDirectoryHash($directory);

                // log that the appserver has been restarted successfull
                $this->getSystemLogger()->debug(
                    sprintf(
                        "appserver has successfully been restarted"
                    )
                );
                
            } else { // if no changes has been found, wait a second
                sleep(1);
            }
        }
    }
    
    /**
     * Returns the time when the contents of the file were changed. The time 
     * returned is a UNIX timestamp.
     * 
     * @param \SplFileInfo $directory The deployment directory
     * 
     * @return integer The UNIX timestamp with the last successfully deplomyent date
     */
    public function getLastSuccessfullyDeployment(\SplFileInfo $directory)
    {
        
        // clear the stat cache to get real mtime if changed
        clearstatcache();
        
        // try to open the flag file with the last successfull deployment UNIX timestamp
        $file = new \SplFileInfo(
            $directory . DIRECTORY_SEPARATOR . ExtractorInterface::FILE_DEPLOYMENT_SUCCESSFULL
        );
        
        // return the change date (last successfull deployment date)
        return $file->getMTime();
    }
        
    /**
     * Calculates an hash value for all files with the extensions .dodeploy 
     * + .deployed. This is used to test if the hash value changed, so if 
     * it changed, the server has to be restarted because a PHAR archive 
     * has to be deployed or undepoyed.
     *  
     * @param \SplFileInfo $directory The deployment directory to watch
     * 
     * @return string The hash value build out of the found filenames
     */
    public function getDirectoryHash(\SplFileInfo $directory)
    {

        // prepeare the array with the file extensions of the files used to build the hash
        $extensionsToWatch = array('dodeploy', 'deployed');
        
        // prepare the array
        $files = new \ArrayObject();
        
        // add all files with the found extensions to the array
        foreach (new \DirectoryIterator($directory) as $fileInfo) {
            if ($fileInfo->isFile() && in_array($fileInfo->getExtension(), $extensionsToWatch)) {
                $files->append($fileInfo->getFilename());
            }
        }

        // calculate and return the hash value for the array
        return md5($files->serialize());
    }
    
    /**
     * Restart the appserver using the appserverctl file in the sbin folder.
     * 
     * @return void
     * @todo This actually only works on Mac OS X 
     */
    public function restart()
    {
        exec(
            APPSERVER_BP . DIRECTORY_SEPARATOR . 'sbin' . DIRECTORY_SEPARATOR . 'appserverctl restart'
        );
    }
}
