<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\CertificateNode
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * Node class which represents the Certificate node of the configuration.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CertificateNode extends AbstractNode
{
    /**
     * The domain on which the certificate should be used
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $domain;

    /**
     * The path to the certification file
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $certPath;

    /**
     * Returns the domain on which the certificate should be used
     *
     * @return string the domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Returns the path to the certification file
     *
     * @return string the path to the certification file
     */
    public function getCertPath()
    {
        return $this->certPath;
    }
}
