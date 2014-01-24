<?php

/**
 * TechDivision\ApplicationServer\Utilities\DirectoryKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Utilities;

/**
 * Utility class that contains keys for directories necessary to
 * run the appserver.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class DirectoryKeys
{

    /**
     * Key for the webapps directory.
     *
     * @var string
     */
    const WEBAPPS = 'webapps';

    /**
     * Key for the temporary directory.
     *
     * @var string
     */
    const TMP = 'tmp';

    /**
     * Key for the log directory.
     *
     * @var string
     */
    const LOG = 'var/log';

    /**
     * Key for the deployment directory.
     *
     * @var string
     */
    const DEPLOY = 'deploy';

    /**
     * This is a utility class, so protect it agains direct
     * instanciation.
     *
     * @return void
     */
    private function __construct()
    {}

    /**
     * This is a utility class, so protect it agains cloning.
     */
    private function __clone()
    {}

    /**
     * Returns the application server's directory structure,
     * all directories has to be relative to the base path.
     *
     * @return array The directory structure
     * @todo Has to be extended for all necessary directories
     */
    public static function getDirectories()
    {
        return array(
            DirectoryKeys::WEBAPPS,
            DirectoryKeys::TMP,
            DirectoryKeys::DEPLOY,
            DirectoryKeys::LOG
        );
    }
}