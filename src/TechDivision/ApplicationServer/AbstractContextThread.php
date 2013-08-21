<?php

/**
 * TechDivision\ApplicationServer\AbstractContainer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

/**
 * An abstraction context layer for Threads.
 * It will automatically register the intitialContext object.
 *
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <jz@techdivision.com>
 */
abstract class AbstractContextThread extends AbstractThread
{
    /**
     * Holds the initialContext object
     *
     * @var \Stackable
     */
    public $initialContext;

    /**
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @params \Stackable $initialContext
     * @return void
     */
    public function __construct($initialContext)
    {
        // get function args
        $functionArgs = func_get_args();
        // shift first arg to initialContext which should be a stackable implementation.
        $this->initialContext = array_shift($functionArgs);
        // call parent
        call_user_func_array(array('parent', '__construct'), $functionArgs);
    }

    /**
     * Returns the initialContext object
     *
     * @return \Stackable
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

}