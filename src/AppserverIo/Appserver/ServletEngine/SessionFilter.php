<?php

/**
 * AppserverIo\Appserver\ServletEngine\FilterIterator
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

namespace AppserverIo\Appserver\ServletEngine;

/**
 * A filter implementation to make sure, that only the newest, configurable session
 * files are preloaded when the session manager has been initialized.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SessionFilter extends \FilterIterator
{

    /**
     * The maximum age of session files we want to load.
     *
     * @var integer
     */
    protected $maximumAge;

    /**
     * Prepares filter with the iterator and the maximum age of session
     * files we want to compare to.
     *
     * @param \Iterator $iterator   The iterator with the files we want to compare to
     * @param integer   $maximumAge The maximum age of the session files we want to load
     */
    public function __construct(\Iterator $iterator, $maximumAge)
    {

        // call parent contructor
        parent::__construct($iterator);

        // initialize the maximum age of the session files we want to load
        $this->maximumAge = $maximumAge;
    }

    /**
     * This method compares the session files age against the confiugured
     * maximum age of session files we want to load.
     *
     * @return boolean TRUE if we want to load the session, else FALS
     */
    public function accept()
    {

        // load the current file
        $splFileInfo = $this->getInnerIterator()->current();

        // calculate the maxiumum age of sessions we want to load
        $aTime = time() - $this->maximumAge;

        // compare the session files age agains the maximum age
        if ($splFileInfo->getATime() < $aTime) {
            return false;
        }

        return true;
    }
}
