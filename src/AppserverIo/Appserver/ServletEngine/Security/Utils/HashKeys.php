<?php

namespace AppserverIo\Appserver\ServletEngine\Security\Utils;

/**
 * @author    Alexandros Weigl <a.weigl@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 */
class HashKeys
{
    /**
     * They key for the "md5" hash algorithm
     *
     * @var string
     */
    const MD5 = 'md5';

    /**
     * They key for the "sha1" hash algorithm
     *
     * @var string
     */
    const SHA1 = 'sha1';

    /**
     * They key for the "sha256" hash algorithm
     *
     * @var string
     */
    const SHA256 = 'sha256';

    /**
     * They key for the "sha512" hash algorithm
     *
     * @var string
     */
    const SHA512 = 'sha512';
}
