<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\AbstractScanner
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core\Scanner\Mock;

use AppserverIo\Appserver\Core\Scanner\AbstractScanner;

/**
 * Abstract scanner which provides basic functionality to its children.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.techdivision.com/
 */
class MockAbstractScanner extends AbstractScanner
{

    /**
     * Returns the systems configuration root directory aka "etc"
     *
     * @return string
     */
    protected function getEtcDir()
    {
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR);
    }

    /**
     * This method will check for the Linux release file normally stored in /etc and will return
     * the version of the distribution
     *
     * @param string|null $distribution Distribution to search a version for
     * @param array       $etcList      List of already collected AND flipped release files we need to filter
     *
     * @return string|boolean
     */
    public function testableGetDistributionVersion($distribution = null, $etcList = array())
    {
        return $this->getDistributionVersion($distribution, $etcList);
    }

    /**
     * Returns an array with file extensions that are used
     * to create the directory hash.
     *
     * @return array The array with the file extensions
     * @see \AppserverIo\Appserver\Core\Scanner\AbstractScanner::getDirectoryHash()
     */
    protected function getExtensionsToWatch()
    {
        // TODO: Implement getExtensionsToWatch() method.
    }

    /**
     * The thread implementation main method which will be called from run in abstractness
     *
     * @return void
     */
    public function main()
    {
        // TODO: Implement main() method.
    }
}
