<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\CertificatesNodeTrait
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

use AppserverIo\Description\Annotations as DI;

/**
 * This trait is used to give any node class the possibility to manage certificates nodes
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait CertificatesNodeTrait
{

    /**
     * The certificates specified within the parent node
     *
     * @var array
     * @DI\Mapping(nodeName="certificates/certificate", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\CertificateNode")
     */
    protected $certificates = array();

    /**
     * Will return the certificates array.
     *
     * @return array The array with the certificate nodes
     */
    public function getCertificates()
    {
        return $this->certificates;
    }

    /**
     * Returns the certificates as an associative array.
     *
     * @return array The array with certificates
     */
    public function getCertificatesAsArray()
    {
        // Iterate over certificates nodes and convert to an array
        $certificates = array();
        /** @var \AppserverIo\Appserver\Core\Api\Node\CertificateNode $certificateNode */
        foreach ($this->getCertificates() as $certificateNode) {
            // restructure to an array
            $certificates[] = array(
                'domain' => $certificateNode->getDomain(),
                'certPath' => $certificateNode->getCertPath()
            );
        }

        // return certificates array
        return $certificates;
    }
}
