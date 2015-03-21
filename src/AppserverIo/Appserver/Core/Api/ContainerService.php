<?php

/**
 * \AppserverIo\Appserver\Core\Api\ContainerService
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

namespace AppserverIo\Appserver\Core\Api;

/**
 * A service that handles container configuration data.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ContainerService extends AbstractFileOperationService
{

    /**
     * Creates the SSL file passed as parameter or nothing if the file already exists.
     *
     * @param \SplFileInfo $certificate The file info about the SSL file to generate
     *
     * @return void
     *
     * @throws \Exception
     */
    public function createSslCertificate(\SplFileInfo $certificate)
    {

        // first we've to check if OpenSSL is available
        if (!$this->isOpenSslAvailable()) {
            return;
        }

        // do nothing if the file is already available
        if ($certificate->isFile()) {
            return;
        }

        // prepare the certificate data from our configuration
        $dn = array(
            "countryName" => "DE",
            "stateOrProvinceName" => "Bavaria",
            "localityName" => "Kolbermoor",
            "organizationName" => "appserver.io",
            "organizationalUnitName" => "Development",
            "commonName" => gethostname(),
            "emailAddress" => "info@appserver.io"
        );

        // check the operating system
        switch ($this->getOsIdentifier()) {

            case 'DAR': // on Mac OS X use the system default configuration

                $configargs = array('config' => $this->getBaseDirectory('/ssl/openssl.cnf'));
                break;

            case 'WIN': // on Windows use the system configuration we deliver

                $configargs = array('config' => $this->getBaseDirectory('/php/extras/ssl/openssl.cnf'));
                break;

            default: // on all other use a standard configuration

                $configargs = array(
                    'digest_alg' => 'md5',
                    'x509_extensions' => 'v3_ca',
                    'req_extensions'   => 'v3_req',
                    'private_key_bits' => 2048,
                    'private_key_type' => OPENSSL_KEYTYPE_RSA,
                    'encrypt_key' => false
                );
        }

        // generate a new private (and public) key pair
        $privkey = openssl_pkey_new($configargs);

        // Generate a certificate signing request
        $csr = openssl_csr_new($dn, $privkey, $configargs);

        // create a self-signed cert that is valid for 365 days
        $sscert = openssl_csr_sign($csr, null, $privkey, 365, $configargs);

        // export the cert + pk files
        $certout = '';
        $pkeyout = '';
        openssl_x509_export($sscert, $certout);
        openssl_pkey_export($privkey, $pkeyout, null, $configargs);

        // write the SSL certificate data to the target
        $file = $certificate->openFile('w');
        if (($written = $file->fwrite($certout . $pkeyout)) === false) {
            throw new \Exception(sprintf('Can\'t create SSL certificate %s', $certificate->getPathname()));
        }

        // log a message that the file has been written successfully
        $this->getInitialContext()->getSystemLogger()->info(
            sprintf('Successfully created %s with %d bytes', $certificate->getPathname(), $written)
        );

        // log any errors that occurred here
        while (($e = openssl_error_string()) !== false) {
            $this->getInitialContext()->getSystemLogger()->debug($e);
        }
    }

    /**
     * Return's all container node configurations.
     *
     * @return array An array with container node configurations
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        return $this->getSystemConfiguration()->getContainers();
    }

    /**
     * Returns the application base directory for the container
     * with the passed UUID.
     *
     * @param string $uuid UUID of the container to return the application base directory for
     *
     * @return string The application base directory for this container
     */
    public function getAppBase($uuid)
    {
        return $this->load($uuid)->getHost()->getAppBase();
    }

    /**
     * Returns true if the OpenSSL extension is loaded, false otherwise
     *
     * @return boolean
     *
     * @codeCoverageIgnore this will most likely always be mocked/stubbed, and it is trivial anyway
     */
    protected function isOpenSslAvailable()
    {
        return extension_loaded('openssl');
    }

    /**
     * Returns the container for the passed UUID.
     *
     * @param string $uuid Unique UUID of the container to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContainerNode The container with the UUID passed as parameter
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::load($uuid)
     */
    public function load($uuid)
    {
        $containers = $this->findAll();
        if (array_key_exists($uuid, $containers)) {
            return $containers[$uuid];
        }
    }
}
