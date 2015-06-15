<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ExecuteNode
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer information about a script that has to be executed.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ExecuteNode extends AbstractArgsNode
{

    /**
     * The script to be executed.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $script;

    /**
     * The directory to execute the script from.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $directory;

    /**
     * Return's the script to be executed
     *
     * @return string The script
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Set's the directory to execute the script from
     *
     * @param string $directory The directory
     *
     * @return void
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Return's the directory to execute the script from
     *
     * @return string The directory
     */
    public function getDirectory()
    {
        return $this->directory;
    }
    /**
     * Prepares the command to be excuted based on the script and
     * the arguments.
     *
     * @return string The command to execute
     */
    public function __toString()
    {

        // initialize the command with the script name
        $command = $this->script;

        // append the arguments
        /** @var \AppserverIo\Appserver\Core\Api\Node\ArgNode $arg */
        foreach ($this->getArgs() as $arg) {
            if ($name = $arg->getName()) {
                $command .=  ' ' . $name;
            }
            if ($value = $arg->castToType()) {
                $command .= ' ' . $value;
            }
        }

        // return the initialized command
        return $command;
    }
}
