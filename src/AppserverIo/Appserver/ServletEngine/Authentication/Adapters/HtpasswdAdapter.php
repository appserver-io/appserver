<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\Adapters\HtpasswdAdapter
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
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authentication\Adapters;

use AppserverIo\Psr\Servlet\Servlet;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationAdapter;

/**
 * Authentication adapter for htpasswd file.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Florian Sydekum <fs@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class HtpasswdAdapter extends AuthenticationAdapter
{

    /**
     * The content of the htpasswd file.
     *
     * @var string
     */
    protected $htpasswd;

    /**
     * Initializes the adapter.
     *
     * @return void
     */
    public function init()
    {

        // get content of htpasswd file.
        $htpasswdData = file($this->getFilename());
        
        // prepare htpasswd entries
        $this->htpasswd = array();
        foreach ($htpasswdData as $entry) {
            list($user, $pwd) = explode(':', $entry);
            $this->htpasswd[$user] = trim($pwd);
        }
    }

    /**
     * Authenticates a user/password combination.
     *
     * @param string $user The username
     * @param string $pwd  The password
     *
     * @return boolean TRUE if authentication was successfull, else FALSE
     */
    public function authenticate($user, $pwd)
    {
        // if user is valid
        if ($this->htpasswd[$user]) {

            if ($this->checkPlainMd5($pwd, $this->htpasswd[$user])) {
                return true;
            } elseif ($this->checkApr1Md5($pwd, $this->htpasswd[$user])) {
                return true;
            } elseif ($this->checkCrypt($pwd, $this->htpasswd[$user])) {
                return true;
            } elseif ($this->checkSha1($pwd, $this->htpasswd[$user])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if htpasswd password is md5 hashed and if clearTextPassword is equal.
     *
     * @param string $clearTextPassword The password plaintext
     * @param string $hashedPassword    The password hashed
     *
     * @return boolean TRUE if the passwords matches, else FALSE
     */
    protected function checkPlainMd5($clearTextPassword, $hashedPassword)
    {
        if (md5($clearTextPassword) == $hashedPassword) {
            return true;
        }
        return false;
    }

    /**
     * Check if htpasswd password is apr1-md5 hashed and if clearTextPassword is not relevant.
     *
     * @param string $clearTextPassword The password plaintext
     * @param string $hashedPassword    The password hashed
     *
     * @return boolean TRUE if the passwords matches, else FALSE
     */
    protected function checkApr1Md5($clearTextPassword, $hashedPassword)
    {
        //if hash starts with $apr1$
        if (strpos($hashedPassword, "$"."apr1"."$") === 0) {
            //strip $arp1$ from string
            $hash = substr($hashedPassword, 6);
            // return string until fist "$"
            $salt = strstr($hash, "$", true);
            $newHashedPassword = $this->generateCryptApr1Md5($clearTextPassword, $salt);
            if ($newHashedPassword == $hashedPassword) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if htpasswd password is crypt hashed and if clearTextPassword is eqal. The
     * following crypt hashes are allowed: DES, MD5 (salted), Blowfish, SHA-256, SHA-512
     *
     * @param string $clearTextPassword The password plaintext
     * @param string $hashedPassword    The password hashed
     *
     * @return boolean TRUE if the passwords matches, else FALSE
     */
    protected function checkCrypt($clearTextPassword, $hashedPassword)
    {
        // since PHP 5.5 crypt passwords can easily check by this function
        if (password_verify($clearTextPassword, $hashedPassword)) {
            return true;
        }
        return false;
    }

    /**
     * Check if htpasswd password is sha hashed and if clearTextPassword is equal
     *
     * @param string $clearTextPassword The password plaintext
     * @param string $hashedPassword    The password hashed
     *
     * @return boolean TRUE if the passwords matches, else FALSE
     */
    protected function checkSha1($clearTextPassword, $hashedPassword)
    {
        if (base64_encode(sha1($clearTextPassword, true)) == $hashedPassword) {
            return true;
        }
        return false;
    }

    /**
     * Returns the servlet instance.
     *
     * @return \AppserverIo\Psr\Servlet\Servlet The servlet instance
     */
    public function getServlet()
    {
        return $this->servlet;
    }

    /**
     * Generates a apr1-md5 (apache compatible) password hash.
     *
     * @param string $plainpasswd The password in plaintext
     * @param string $salt        The salt
     *
     * @return string The salted password hash
     */
    protected function generateCryptApr1Md5($plainpasswd, $salt = null)
    {
        if (!$salt) {
            $salt = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
        }
        $len = strlen($plainpasswd);
        $text = $plainpasswd.'$apr1$'.$salt;
        $bin = pack("H32", md5($plainpasswd.$salt.$plainpasswd));
        for ($i = $len; $i > 0; $i -= 16) {
            $text .= substr($bin, 0, min(16, $i));
        }
        for ($i = $len; $i > 0; $i >>= 1) {
            $text .= ($i & 1) ? chr(0) : $plainpasswd{0};
        }
        $bin = pack("H32", md5($text));
        for ($i = 0; $i < 1000; $i++) {
            $new = ($i & 1) ? $plainpasswd : $bin;
            if ($i % 3) {
                $new .= $salt;
            }
            if ($i % 7) {
                $new .= $plainpasswd;
            }
            $new .= ($i & 1) ? $bin : $plainpasswd;
            $bin = pack("H32", md5($new));
        }
        $tmp = "";
        for ($i = 0; $i < 5; $i++) {
            $k = $i + 6;
            $j = $i + 12;
            if ($j == 16) {
                $j = 5;
            }
            $tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
        }
        $tmp = chr(0).chr(0).$bin[11].$tmp;
        $tmp = strtr(
            strrev(
                substr(
                    base64_encode($tmp),
                    2
                )
            ),
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
            "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
        );
        return "$"."apr1"."$".$salt."$".$tmp;
    }
}
