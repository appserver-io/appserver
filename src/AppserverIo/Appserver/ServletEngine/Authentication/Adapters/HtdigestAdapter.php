<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\Adapters\HtdigestAdapter
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
 * @author     Florian Sydekum <fs@techdivision.com>
 * @author     Philipp Dittert <pd@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authentication\Adapters;

use AppserverIo\Psr\Servlet\Servlet;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationAdapter;

/**
 * Authentication adapter for htdigest file.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Florian Sydekum <fs@techdivision.com>
 * @author     Philipp Dittert <pd@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class HtdigestAdapter extends AuthenticationAdapter
{
    /**
     * The content of the htdigest file.
     *
     * @var array
     */
    protected $htdigest = array();

    /**
     * Initializes the adapter.
     *
     * @return void
     */
    public function init()
    {

        // get content of htdigest file.
        $htDigestData = file($this->getFilename());

        // prepare htdigest entries
        foreach ($htDigestData as $entry) {
            list($user, $realm, $hash) = explode(':', $entry);
            $this->htdigest[$user] = array('user'=>$user, 'realm'=>$realm, 'hash'=>trim($hash));
        }
    }

    /**
     * Authenticates a user/realm/H1 hash combination.
     *
     * @param array  $data      The auth data
     * @param string $reqMethod The request method, e. g. GET or POST
     *
     * @return boolan TRUE if authentication was successfull, else FALSE
     */
    public function authenticate($data, $reqMethod)
    {
        // if user is valid
        $credentials = $this->getHtDigest();
        $user = $data['username'];
        if ($credentials[$user] && $credentials[$user]['realm'] == $data['realm']) {
            $HA1 = $credentials[$user]['hash'];
            $HA2 = md5($reqMethod.":".$data['uri']);
            $middle = ':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':';
            $response = md5($HA1.$middle.$HA2);

            if ($data['response'] == $response) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns htdigest credential array.
     *
     * @return array The credentials
     */
    protected function getHtDigest()
    {
        return $this->htdigest;
    }
}
