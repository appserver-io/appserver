<?php

/**
 * AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application\Interfaces;

/**
 * Interface for manager settings.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ManagerSettingsInterface
{

    /**
     * Set's the base directory which may contain additional configuration informations.
     *
     * @param string $baseDirectory The base directory
     *
     * @return void
     */
    public function setBaseDirectory($baseDirectory);

    /**
     * Return's the base directory which may contain additional configuration informations.
     *
     * @return string The base directory
     */
    public function getBaseDirectory();

    /**
     * Merge the passed params with the settings.
     *
     * @param array $params The associative array with the params to merge
     *
     * @return void
     */
    public function mergeWithParams(array $params);
}
