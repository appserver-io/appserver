<?php

/**
 * AppserverIo\Appserver\Meta\Composer\Script\SetupKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Meta
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Meta\Composer\Script;

/**
 * Constants used for Composer setup script.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Meta
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class SetupKeys
{

    /**
     * OS signature for 'darwin'.
     *
     * @var string
     */
    const OS_DARWIN = 'darwin';

    /**
     * OS signature for 'debian'.
     *
     * @var string
     */
    const OS_DEBIAN = 'debian';

    /**
     * OS signature for 'ubuntu'
     *
     * @var string
     */
    const OS_UBUNTU ='ubuntu';

    /**
     * OS signature for 'fedora'.
     *
     * @var string
     */
    const OS_FEDORA = 'fedora';

    /**
     * OS signature for 'redhat'.
     *
     * @var string
     */
    const OS_REDHAT = 'redhat';

    /**
     * OS signature for 'centOS'.
     *
     * @var string
     */
    const OS_CENTOS = 'centOS';

    /**
     * OS signature for 'windows'.
     *
     * @var string
     */
    const OS_WINDOWS = 'windows';

    /**
     * OS signature for 'arch'.
     *
     * @var string
     */
    const OS_ARCH = 'arch';

    /**
     * OS signature when calling php_uname('s') on Mac OS x 10.8.x/10.9.x.
     *
     * @var string
     */
    const OS_FAMILY_DARWIN = 'darwin';

    /**
     * OS signature when calling php_uname('s') on Linux Debian/Ubuntu/Fedora and CentOS.
     *
     * @var string
     */
    const OS_FAMILY_LINUX = 'linux';

    /**
     * OS signature when calling php_uname('s') on Windows.
     *
     * @var string
     */
    const OS_FAMILY_WINDOWS = 'windows';

    /**
     * Configuration key for 'appserver.software.identifier'.
     *
     * @var string
     */
    const SOFTWARE_IDENTIFIER = 'appserver.software.identifier';

    /**
     * Configuration key for 'appserver.os.family'.
     *
     * @var string
     */
    const OS_FAMILY = 'appserver.os.family';

    /**
     * OS signature for 'appserver.os.distribution'.
     *
     * @var string
     */
    const OS_IDENTIFIER = 'appserver.os.identifier';

    /**
     * OS signature for 'appserver.os.architecture'.
     *
     * @var string
     */
    const OS_ARCHITECTURE = 'appserver.os.architecture';

    /**
     * Configuration key for 'appserver.install.dir'.
     *
     * @var string
     */
    const INSTALL_DIR = 'appserver.install.dir';

    /**
     * Default configuration for the host values.
     *
     * @var string
     */
    const DEFAULT_HOST = '127.0.0.1';

    /**
     * Configuration key for 'appserver.php.version'.
     *
     * @var string
     */
    const PHP_VERSION = 'appserver.php.version';

    /**
     * Configuration key for 'appserver.version'.
     *
     * @var string
     */
    const VERSION = 'appserver.version';

    /**
     * Configuration key for 'appserver.release.name'.
     *
     * @var string
     */
    const RELEASE_NAME = 'appserver.release.name';

    /**
     * Configuration key for 'appserver.admin.email'.
     *
     * @var string
     */
    const ADMIN_EMAIL = 'appserver.admin.email';

    /**
     * Configuration key for 'appserver.container.server.worker.acceptMin'.
     *
     * @var string
     */
    const CONTAINER_SERVER_WORKER_ACCEPT_MIN = 'appserver.container.server.worker.accept.min';

    /**
     * Configuration key for 'appserver.container.server.worker.acceptMax'.
     *
     * @var string
     */
    const CONTAINER_SERVER_WORKER_ACCEPT_MAX = 'appserver.container.server.worker.accept.max';

    /**
     * Configuration key for 'appserver.container.http.worker.number'.
     *
     * @var string
     */
    const CONTAINER_HTTP_WORKER_NUMBER = 'appserver.container.http.worker.number';

    /**
     * Configuration key for 'appserver.container.http.host'.
     *
     * @var string
     */
    const CONTAINER_HTTP_HOST = 'appserver.container.http.host';

    /**
     * Configuration key for 'appserver.container.http.port'.
     *
     * @var string
     */
    const CONTAINER_HTTP_PORT = 'appserver.container.http.port';

    /**
     * Configuration key for 'appserver.container.https.worker.number'.
     *
     * @var string
     */
    const CONTAINER_HTTPS_WORKER_NUMBER = 'appserver.container.https.worker.number';

    /**
     * Configuration key for 'appserver.container.https.host'.
     *
     * @var string
     */
    const CONTAINER_HTTPS_HOST = 'appserver.container.https.host';

    /**
     * Configuration key for 'appserver.container.https.port'.
     *
     * @var string
     */
    const CONTAINER_HTTPS_PORT = 'appserver.container.https.port';

    /**
     * Configuration key for 'appserver.container.persistence-container.worker.number'.
     *
     * @var string
     */
    const CONTAINER_PERSISTENCE_CONTAINER_WORKER_NUMBER = 'appserver.container.persistence-container.worker.number';

    /**
     * Configuration key for 'appserver.container.persistence-container.host'.
     *
     * @var string
     */
    const CONTAINER_PERSISTENCE_CONTAINER_HOST = 'appserver.container.persistence-container.host';

    /**
     * Configuration key for 'appserver.container.persistence-container.port'.
     *
     * @var string
     */
    const CONTAINER_PERSISTENCE_CONTAINER_PORT = 'appserver.container.persistence-container.port';

    /**
     * Configuration key for 'appserver.container.memcached.worker.number'.
     *
     * @var string
     */
    const CONTAINER_MEMCACHED_WORKER_NUMBER = 'appserver.container.memcached.worker.number';

    /**
     * Configuration key for 'appserver.container.memcached.host'.
     *
     * @var string
     */
    const CONTAINER_MEMCACHED_HOST = 'appserver.container.memcached.host';

    /**
     * Configuration key for 'appserver.container.memcached.port'.
     *
     * @var string
     */
    const CONTAINER_MEMCACHED_PORT = 'appserver.container.memcached.port';

    /**
     * Configuration key for 'appserver.container.message-queue.worker.number'.
     *
     * @var string
     */
    const CONTAINER_MESSAGE_QUEUE_WORKER_NUMBER = 'appserver.container.message-queue.worker.number';

    /**
     * Configuration key for 'appserver.container.message-queue.host'.
     *
     * @var string
     */
    const CONTAINER_MESSAGE_QUEUE_HOST = 'appserver.container.message-queue.host';

    /**
     * Configuration key for 'appserver.container.message-queue.port'.
     *
     * @var string
     */
    const CONTAINER_MESSAGE_QUEUE_PORT = 'appserver.container.message-queue.port';

    /**
     * Configuration key for 'appserver.container.web-socket.host'.
     *
     * @var string
     */
    const CONTAINER_WEB_SOCKET_HOST = 'appserver.appserver.container.web-socket.host';

    /**
     * Configuration key for 'container.web-socket.port'.
     *
     * @var string
     */
    const CONTAINER_WEB_SOCKET_PORT= 'appserver.appserver.container.web-socket.port';

    /**
     * Configuration key for 'appserver.php-fpm.port'.
     *
     * @var string
     */
    const PHP_FPM_PORT = 'appserver.php-fpm.port';

    /**
     * Configuration key for 'php-fpm.host'.
     *
     * @var string
     */
    const PHP_FPM_HOST = 'appserver.php-fpm.host';

    /**
     * Configuration key for 'appserver.umask'.
     *
     * @var string
     */
    const UMASK = 'appserver.umask';

    /**
     * Configuration key for 'appserver.user'.
     *
     * @var string
     */
    const USER = 'appserver.user';

    /**
     * Configuration key for 'appserver.group'.
     *
     * @var string
     */
    const GROUP = 'appserver.group';
}
