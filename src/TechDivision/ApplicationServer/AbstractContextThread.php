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
     * (non-PHPdoc)
     * 
     * @see \TechDivision\ApplicationServer\AbstractThread::run()
     */
    public function run()
    {
        // register the class loader again, because in a Thread the context has been lost maybe
        $this->getInitialContext()->getClassLoader()->register(true);

        // call the parent run method to start the thread
        parent::run();
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
    
    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     * 
     * @param string $className The class name to create the instance of
     * @param array $args The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }
}