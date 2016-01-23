<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Authenticator\DigestAuthentication
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @author    Bernahrd Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/http
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authenticator;

use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Http\Authentication\Adapters\HtdigestAdapter;

/**
 * Class DigestAuthentication
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @author    Bernahrd Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/http
 * @link      http://www.appserver.io
 */
class DigestAuthenticator extends BasicAuthenticator
{

    /**
     * Defines the default authentication adapter used if none was specified
     *
     * @var string
     */
    const DEFAULT_ADAPTER = HtdigestAdapter::ADAPTER_TYPE;

    /**
     * Defines the auth type which should match the client request type definition
     *
     * @var string
     */
    const AUTH_TYPE = 'Digest';

    /**
     * Constructs the authentication type
     *
     * @param array $configData The configuration data for auth type instance
     */
    public function __construct(array $configData = array())
    {

        // initialize the supported adapter types
        $this->addSupportedAdapter(HtdigestAdapter::getType());

        // initialize the instance
        parent::__construct($configData);
    }

    /**
     * Parses the request for the necessary, authentication adapter specific, login credentials.
     *
     * @param \AppserverIo\Psr\HttpMessage\RequestInterface $request The request with the content of authentication data sent by client
     *
     * @return void
     */
    protected function parse(RequestInterface $request)
    {

        // load the raw login credentials
        $rawAuthData = $request->getHeader(Protocol::HEADER_AUTHORIZATION);

        // init data and matches arrays
        $data = array();
        $matches = array();

        // define required data
        $requiredData = array(
            'realm' => 1,
            'nonce' => 1,
            'nc' => 1,
            'cnonce' => 1,
            'qop' => 1,
            'username' => 1,
            'uri' => 1,
            'response' => 1
        );

        // prepare key for parsing logic
        $key = implode('|', array_keys($requiredData));

        // parse header value
        preg_match_all('@(' . $key . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $rawAuthData, $matches, PREG_SET_ORDER);

        // iterate all found values for header value
        foreach ($matches as $match) {
            // check if match could be found
            if ($match[3]) {
                $data[$match[1]] = $match[3];
            } else {
                $data[$match[1]] = $match[4];
            }

            // unset required value because we got it processed
            unset($requiredData[$match[1]]);
        }

        // set if all required data was processed
        $data['method'] = $this->getRequestMethod();
        $this->authData = $requiredData ? false : $data;
    }

    /**
     * Returns the authentication header for response to set
     *
     * @return string
     */
    public function getAuthenticateHeader()
    {
        return $this->getType() . ' realm="' . $this->configData["realm"] . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($this->configData["realm"]) . '"';
    }
}
