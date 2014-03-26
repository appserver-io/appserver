<?php

/**
 * TechDivision\ApplicationServer\Provisioning\AbstractStep
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

use TechDivision\ApplicationServer\Api\Node\StepNode;

/**
 * Abstract base class for a step implementation.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractStep implements Step
{

    /**
     * The step node with the configuration data for this step.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\StepNode
     */
    protected $stepNode;
    
    /**
     * The absolute path to the appserver PHP executable.
     * 
     * @var string
     */
    protected $phpExecutable;
    
    /**
     * The absolute path to the applications folder.
     * 
     * @var string
     */
    protected $webappPath;
    
    /**
     * Injects the step node with the configuration data for this step.
     * 
     * @param \TechDivision\ApplicationServer\Api\Node\StepNode $stepNode The step node data
     * 
     * @return void
     */
    public function injectStepNode(StepNode $stepNode)
    {
        $this->stepNode = $stepNode;
    }
    
    /**
     * Injects the absolute path to the appservers PHP executable.
     * 
     * @param string $phpExecutable The absolute path to the appservers PHP executable
     * 
     * @return void
     */
    public function injectPhpExecutable($phpExecutable)
    {
        $this->phpExecutable = $phpExecutable;
    }
    
    /**
     * Injects the absolute path to the applications folder.
     * 
     * @param string $webappPath The absolute path to applications folder
     * 
     * @return void
     */
    public function injectWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }
    
    /**
     * Returns the step node data.
     * 
     * @return \TechDivision\ApplicationServer\Api\Node\StepNode $stepNode The step node data
     */
    public function getStepNode()
    {
        return $this->stepNode;
    }
    
    /**
     * Returns the absolute path to the appservers PHP executable.
     * 
     * @return string The absolute path to the appservers PHP executable
     */
    public function getPhpExecutable()
    {
        return $this->phpExecutable;
    }
    
    /**
     * Returns the absolute path to the applications folder.
     * 
     * @return string The applications folder
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }
}
