<?php

/**
 * \AppserverIo\Appserver\Core\ServerNodeConfiguration
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Utilities\SslOptionKeys;
use AppserverIo\Appserver\Core\Api\Node\ServerNodeInterface;
use AppserverIo\Server\Interfaces\ServerConfigurationInterface;

/**
 * Wrapper for the sever node passed from the appserver-io-psr/server package.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServerNodeConfiguration implements ServerConfigurationInterface
{

    /**
     * The server node instance.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ServerNodeInterface
     */
    protected $node;

    /**
     * The analytics array.
     *
     * @var array
     */
    protected $analytics;

    /**
     * The headers array.
     *
     * @var array
     */
    protected $headers;

    /**
     * The handlers array.
     *
     * @var array
     */
    protected $handlers;

    /**
     * Hold's the connection handler array
     *
     * @var array
     */
    protected $connectionHandlers;

    /**
     * The virtual hosts array.
     *
     * @var array
     */
    protected $virtualHosts;

    /**
     * The authentications array.
     *
     * @var array
     */
    protected $authentications;

    /**
     * The modules array.
     *
     * @var array
     */
    protected $modules;

    /**
     * The rewrites array.
     *
     * @var array
     */
    protected $rewrites;

    /**
     * The array with the rewrite maps.
     *
     * @var array
     */
    protected $rewriteMaps;

    /**
     * The accesses array.
     *
     * @var array
     */
    protected $accesses;

    /**
     * The environmentVariables array.
     *
     * @var array
     */
    protected $environmentVariables;

    /**
     * The locations array.
     *
     * @var array
     */
    protected $locations;

    /**
     * Initializes the configuration with the values found in
     * the passed server configuration node.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ServerNodeInterface $node The server node instance
     */
    public function __construct(ServerNodeInterface $node)
    {

        // set the node itself
        $this->node = $node;

        // pre-load the nodes data
        $this->analytics = $node->getAnalyticsAsArray();
        $this->virtualHosts = $node->getVirtualHostsAsArray();
        $this->handlers = $node->getFileHandlersAsArray();
        $this->headers = $node->getHeadersAsArray();
        $this->connectionHandlers = $node->getConnectionHandlersAsArray();
        $this->authentications = $node->getAuthenticationsAsArray();
        $this->rewrites = $node->getRewritesAsArray();
        $this->rewriteMaps = $node->getRewriteMapsAsArray();
        $this->accesses = $node->getAccessesAsArray();
        $this->environmentVariables = $node->getEnvironmentVariablesAsArray();
        $this->locations = $node->getLocationsAsArray();
        $this->certificates = $node->getCertificatesAsArray();

        // modules can have params, so not make them flat
        $this->modules = $node->getModules();
    }

    /**
     * Returns analytics
     *
     * @return string
     */
    public function getAnalytics()
    {
        return $this->analytics;
    }

    /**
     * Returns the username we want to execute the processes with.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->node->getParam('user');
    }

    /**
     * Returns the groupname we want to execute the processes with.
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->node->getParam('group');
    }

    /**
     * Returns the server socket flags.
     *
     * @return string
     */
    public function getFlags()
    {
        return $this->node->getParam('flags');
    }

    /**
     * Returns type
     *
     * @return string
     */
    public function getType()
    {
        return $this->node->getType();
    }

    /**
     * Returns logger name
     *
     * @return string
     */
    public function getLoggerName()
    {
        return $this->node->getLoggerName();
    }

    /**
     * Returns servers name
     *
     * @return string
     */
    public function getName()
    {
        return $this->node->getName();
    }

    /**
     * Returns transport
     *
     * @return string
     */
    public function getTransport()
    {
        return $this->node->getParam('transport');
    }

    /**
     * Returns address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->node->getParam('address');
    }

    /**
     * Returns port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->node->getParam('port');
    }

    /**
     * Returns software
     *
     * @return string
     */
    public function getSoftware()
    {
        return $this->node->getParam('software');
    }

    /**
     * Returns admin
     *
     * @return string
     */
    public function getAdmin()
    {
        return $this->node->getParam('admin');
    }

    /**
     * Returns keep-alive max connection
     *
     * @return int
     */
    public function getKeepAliveMax()
    {
        return (int)$this->node->getParam('keepAliveMax');
    }

    /**
     * Returns keep-alive timeout
     *
     * @return int
     */
    public function getKeepAliveTimeout()
    {
        return (int)$this->node->getParam('keepAliveTimeout');
    }

    /**
     * Returns template file path for errors page
     *
     * @return string
     */
    public function getErrorsPageTemplatePath()
    {
        return (string)$this->node->getParam('errorsPageTemplatePath');
    }

    /**
     * Returns template path for possible configured welcome page
     *
     * @return string
     */
    public function getWelcomePageTemplatePath()
    {
        return (string)$this->node->getParam('welcomePageTemplatePath');
    }

    /**
     * Returns template path for possible configured auto index page
     *
     * @return string
     */
    public function getAutoIndexTemplatePath()
    {
        return (string)$this->node->getParam('autoIndexTemplatePath');
    }

    /**
     * Returns worker number
     *
     * @return int
     */
    public function getWorkerNumber()
    {
        return $this->node->getParam('workerNumber');
    }

    /**
     * Returns worker's accept min count
     *
     * @return int
     */
    public function getWorkerAcceptMin()
    {
        return $this->node->getParam('workerAcceptMin');
    }

    /**
     * Returns worker's accept max count
     *
     * @return int
     */
    public function getWorkerAcceptMax()
    {
        return $this->node->getParam('workerAcceptMax');
    }

    /**
     * Returns the auto index configuration
     *
     * @return boolean
     */
    public function getAutoIndex()
    {
        return $this->node->getParam('autoIndex');
    }

    /**
     * Returns context type
     *
     * @return string
     */
    public function getServerContextType()
    {
        return $this->node->getServerContext();
    }

    /**
     * Returns request type
     *
     * @return string
     */
    public function getRequestContextType()
    {
        return $this->node->getRequestContext();
    }

    /**
     * Returns socket type
     *
     * @return string
     */
    public function getSocketType()
    {
        return $this->node->getSocket();
    }

    /**
     * Returns worker type
     *
     * @return string
     */
    public function getWorkerType()
    {
        return $this->node->getWorker();
    }

    /**
     * Returns document root
     *
     * @return string
     */
    public function getDocumentRoot()
    {
        return $this->node->getParam('documentRoot');
    }

    /**
     * Returns directory index definition
     *
     * @return string
     */
    public function getDirectoryIndex()
    {
        return $this->node->getParam('directoryIndex');
    }

    /**
     * Returns cert path
     *
     * @return string
     */
    public function getCertPath()
    {
        return $this->node->getParam(SslOptionKeys::CERT_PATH);
    }

    /**
     * Returns passphrase
     *
     * @return string
     */
    public function getPassphrase()
    {
        return $this->node->getParam(SslOptionKeys::PASSPHRASE);
    }

    /**
     * Returns DH param path
     *
     * @return string
     */
    public function getDhParamPath()
    {
        return $this->node->getParam(SslOptionKeys::DH_PARAM_PATH);
    }

    /**
     * Return's private key path
     *
     * @return string
     */
    public function getPrivateKeyPath()
    {
        return $this->node->getParam(SslOptionKeys::PRIVATE_KEY_PATH);
    }

    /**
     * Return's the crypto method to use
     *
     * @return string
     */
    public function getCryptoMethod()
    {
        return $this->node->getParam(SslOptionKeys::CRYPTO_METHOD);
    }

    /**
     * Return's the peer name to be used, if this value is not set, then the name is guessed based on the hostname used when opening the stream
     *
     * @return string
     */
    public function getPeerName()
    {
        return $this->node->getParam(SslOptionKeys::PEER_NAME);
    }

    /**
     * Return's TRUE it the verification of use SSL certificate has to be required
     *
     * @return boolean
     */
    public function getVerifyPeer()
    {
        return $this->node->getParam(SslOptionKeys::VERIFY_PEER);
    }

    /**
     * Return's TRUE it the peer name has to be verified
     *
     * @return boolean
     */
    public function getVerifyPeerName()
    {
        return $this->node->getParam(SslOptionKeys::VERIFY_PEER_NAME);
    }

    /**
     * Return's TRUE to disable TLS compression. This can help mitigate the CRIME attack vector
     *
     * @return boolean
     */
    public function getDisableCompression()
    {
        return $this->node->getParam(SslOptionKeys::DISABLE_COMPRESSION);
    }

    /**
     * Return's TRUE if self-signed certificates has to be allowed, but requires verify_peer to be FALSE
     *
     * @return boolean
     */
    public function getAllowSelfSigned()
    {
        return $this->node->getParam(SslOptionKeys::ALLOW_SELF_SIGNED);
    }

    /**
     * Return's TRUE if control cipher ordering preferences during negotiation has to be allowed
     *
     * @return boolean
     */
    public function getHonorCipherOrder()
    {
        return $this->node->getParam(SslOptionKeys::HONOR_CIPHER_ORDER);
    }

    /**
     * Return's the curve to use with ECDH ciphers, if not specified prime256v1 will be used
     *
     * @return string
     */
    public function getEcdhCurve()
    {
        return $this->node->getParam(SslOptionKeys::ECDH_CURVE);
    }

    /**
     * Return's TRUE if a new key pair has to be created in scenarios where ECDH cipher suites are negotiated (instead of the preferred ECDHE ciphers)
     *
     * @return boolean
     */
    public function getSingleEcdhUse()
    {
        return $this->node->getParam(SslOptionKeys::SINGLE_ECDH_USE);
    }

    /**
     * Return's TRUE if new key pair has to be created created when using DH parameters (improves forward secrecy)
     *
     * @return boolean
     */
    public function getSingleDhUse()
    {
        return $this->node->getParam(SslOptionKeys::SINGLE_DH_USE);
    }

    /**
     * Return's the list of available ciphers.
     *
     * @return string
     * @link http://php.net/manual/en/context.ssl.php#context.ssl.ciphers
     * @link https://www.openssl.org/docs/manmaster/apps/ciphers.html#CIPHER_LIST_FORMAT
     */
    public function getCiphers()
    {
        return $this->node->getParam(SslOptionKeys::CIPHERS);
    }

    /**
     * Returns connection handlers
     *
     * @return array
     */
    public function getConnectionHandlers()
    {
        return $this->connectionHandlers;
    }

    /**
     * Returns modules
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Returns handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * Returns headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns virtual hosts
     *
     * @return array
     */
    public function getVirtualHosts()
    {
        return $this->virtualHosts;
    }

    /**
     * Returns the authentication configuration.
     *
     * @return array The array with the authentication configuration
     */
    public function getAuthentications()
    {
        return $this->authentications;
    }

    /**
     * Returns the rewrite configuration.
     *
     * @return array
     */
    public function getRewrites()
    {
        return $this->rewrites;
    }

    /**
     * Returns the access configuration.
     *
     * @return array
     */
    public function getAccesses()
    {
        return $this->accesses;
    }

    /**
     * Returns the environment variable configuration
     *
     * @return array
     */
    public function getEnvironmentVariables()
    {
        return $this->environmentVariables;
    }

    /**
     * Returns the locations.
     *
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Returns the locations.
     *
     * @return array
     */
    public function getRewriteMaps()
    {
        return $this->rewriteMaps;
    }

    /**
     * Returns stream context type
     *
     * @return string
     */
    public function getStreamContextType()
    {
        return $this->node->getStreamContext();
    }

    /**
     * Returns the certificates used by the server
     *
     * @return array
     */
    public function getCertificates()
    {
        return $this->certificates;
    }
}
