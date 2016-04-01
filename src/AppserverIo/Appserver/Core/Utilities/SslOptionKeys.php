<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\SslOptionKeys
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
 * Utility class that contains keys for SSL socket configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SslOptionKeys
{

    /**
     * Key for the 'certPath'.
     *
     * @var string
     */
    const CERT_PATH = 'certPath';

    /**
     * Key for the 'passphrase'.
     *
     * @var string
     */
    const PASSPHRASE = 'passphrase';

    /**
     * Key for the 'dhParamPath'.
     *
     * @var string
     */
    const DH_PARAM_PATH = 'dhParamPath';

    /**
     * Key for the 'privateKeyPath'.
     *
     * @var string
     */
    const PRIVATE_KEY_PATH = 'privateKeyPath';

    /**
     * Key for the 'cryptoMethod'.
     *
     * @var string
     */
    const CRYPTO_METHOD = 'cryptoMethod';

    /**
     * Key for the 'ecdhCurve'.
     *
     * @var string
     */
    const ECDH_CURVE = 'ecdhCurve';

    /**
     * Key for the 'peerName'.
     *
     * @var string
     */
    const PEER_NAME = 'peerName';

    /**
     * Key for the 'verifyPeer'.
     *
     * @var string
     */
    const VERIFY_PEER = 'verifyPeer';

    /**
     * Key for the 'verifyPeerName'.
     *
     * @var string
     */
    const VERIFY_PEER_NAME = 'verifyPeerName';

    /**
     * Key for the 'allowSelfSigned'.
     *
     * @var string
     */
    const ALLOW_SELF_SIGNED = 'allowSelfSigned';

    /**
     * Key for the 'disableCompression'.
     *
     * @var string
     */
    const DISABLE_COMPRESSION = 'disableCompression';

    /**
     * Key for the 'honorCipherOrder'.
     *
     * @var string
     */
    const HONOR_CIPHER_ORDER = 'honorCipherOrder';

    /**
     * Key for the 'singleEcdhUse'.
     *
     * @var string
     */
    const SINGLE_ECDH_USE = 'singleEcdhUse';

    /**
     * Key for the 'singleDhUse'.
     *
     * @var string
     */
    const SINGLE_DH_USE = 'singleDhUse';

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }
}
