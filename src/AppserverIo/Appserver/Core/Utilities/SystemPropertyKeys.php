<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\SystemPropertyKeys
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

namespace AppserverIo\Appserver\Core\Utilities;

/**
 * Utility class that contains the system property keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SystemPropertyKeys extends DirectoryKeys
{

    /**
     * The key of the system property containing the name of the web application.
     *
     * @var string
     */
    const WEBAPP_NAME = 'webapp.name';

    /**
     * The key of the system property containing the name of the web application's data directory.
     *
     * @var string
     */
    const WEBAPP_DATA = 'webapp.data.dir';

    /**
     * The key of the system property containing the name of the web application's cache directory.
     *
     * @var string
     */
    const WEBAPP_CACHE = 'webapp.cache.dir';

    /**
     * The key of the system property containing the name of the web application's session directory.
     *
     * @var string
     */
    const WEBAPP_SESSION = 'webapp.session.dir';

    /**
     * The key of the system property containing the name of the container.
     *
     * @var string
     */
    const CONTAINER_NAME = 'container.name';

    /**
     * The key of the system property containing the path to the container's web application directory.
     *
     * @var string
     */
    const WEBAPP = 'webapp.dir';

    /**
     * The key of the system property containing the host's application base directory (relative to the base directory).
     *
     * @var string
     */
    const HOST_APP_BASE = 'host.appBase.dir';

    /**
     * The key of the system property containing the  host's temporary base directory (relative to the base directory).
     *
     * @var string
     */
    const HOST_TMP_BASE = 'host.tmpBase.dir';

    /**
     * The key of the system property containing the  host's deploy base directory (relative to the base directory).
     *
     * @var string
     */
    const HOST_DEPLOY_BASE = 'host.deployBase.dir';
}
