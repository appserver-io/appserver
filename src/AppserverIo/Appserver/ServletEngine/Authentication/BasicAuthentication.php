<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\BasicAuthentication
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
 * @subpackage Application
 * @author     Philipp Dittert <pd@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Http\HttpProtocol;

/**
 * A basic authentication implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Philipp Dittert <pd@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class BasicAuthentication extends AbstractAuthentication
{
    /**
     * Authenticate against Backend
     *
     * @return bool
     */
    public function authenticate()
    {
        $config = $this->getSecuredUrlConfig();
        $req = $this->getServletRequest();
        $res = $this->getServletResponse();

        $realm = $config['auth']['realm'];
        $adapterType = $config['auth']['adapter_type'];

        // if client provided authentication data
        if ($authorizationData = $req->getHeader(HttpProtocol::HEADER_AUTHORIZATION)) {

            list ($authType, $data) = explode(' ', $authorizationData);

            // handle authentication method and get credentials
            $credentials = null;
            if ($authType == AbstractAuthentication::AUTHENTICATION_METHOD_BASIC) {
                $credentials = $this->basic($data);
            }

            // if credentials are provided and authorization method is the same as configured
            if ($credentials) {

                // get real credentials
                list ($user, $pwd) = explode(':', $credentials);

                // initialize the adapter class
                $authAdapterClass = 'AppserverIo\Appserver\ServletEngine\Authentication\Adapters\\' . ucfirst($adapterType) . 'Adapter';

                // instantiate configured authentication adapter
                $authAdapter = new $authAdapterClass($config);

                // delegate authentication to adapter
                if ($authAdapter->authenticate($user, $pwd)) {
                    return true;
                }
            }
        }

        // either authentication data was not provided or authentication failed
        $res->setStatusCode(401);
        $res->addHeader(HttpProtocol::HEADER_WWW_AUTHENTICATE, AbstractAuthentication::AUTHENTICATION_METHOD_BASIC . ' ' . 'realm="' . $realm . '"');
        $res->appendBodyStream("<html><head><title>401 Authorization Required</title></head><body><h1>401 Authorization Required</h1><p>This server could not verify that you are authorized to access the document requested. Either you supplied the wrong credentials (e.g., bad password), or your browser doesn't understand how to supply the credentials required. Confused</p></body></html>");
        return false;
    }

    /**
     * Handles basic authentication method.
     *
     * @param string $data The data
     *
     * @return string
     */
    protected function basic($data)
    {
        return base64_decode($data);
    }
}
