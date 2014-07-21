<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ParamsNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * Abstract node that serves nodes having a directories/directory child.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
trait DirectoriesNodeTrait
{

    /**
     * The directories.
     *
     * @var array
     * @AS\Mapping(nodeName="directories/directory", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\DirectoryNode")
     */
    protected $directories = array();

    /**
     * Array with the directories.
     *
     * @param array $directories The directories
     *
     * @return void
     */
    public function setDirectories(array $directories)
    {
        $this->directories = $directories;
    }

    /**
     * Array with the directories.
     *
     * @return array
     */
    public function getDirectories()
    {
        return $this->directories;
    }
}
