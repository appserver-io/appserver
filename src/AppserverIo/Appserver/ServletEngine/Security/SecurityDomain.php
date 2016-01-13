<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\SecurityDomain
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

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface;

/**
 * Security domain implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SecurityDomain implements SecurityDomainInterface
{

    /**
     * The security domain's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The security domain's login modules.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface
     */
    protected $configruation;

    /**
     * Initialize the security domain with the passed name.
     *
     * @param string $name The security domain's name
     */
    public function __construct($name)
    {

        // set the passed name
        $this->name = $name;
    }

    /**
     * Return's the name of the security domain.
     *
     * @return string The security domain's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Inject the security domain's configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface $configuration The security domain's configuration
     *
     * @return void
     */
    public function injectConfiguration(SecurityDomainNodeInterface $configuration)
    {
        $this->configruation = $configuration;
    }

    /**
     * Return's the security domain's configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface The security domain's configuration
     */
    public function getConfiguration()
    {
        return $this->configruation;
    }
}
