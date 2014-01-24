<?php

/**
 * TechDivision\ApplicationServer\Stream\SecureReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Stream;

use TechDivision\ApplicationServer\AbstractReceiver;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Johann Zelger <jz@techdivision.com>
 */
class SecureReceiver extends AbstractReceiver
{
    /**
     * Sets up the specific socket instance
     *
     * @return void
     */
    protected function setupSocket() {
        // first call parent setup routine
        parent::setupSocket();
        // set secure receiver params
        $this->getSocket()
            ->setCertPath($this->getCertPath())
            ->setCertPassphrase($this->getCertPassphrase());

    }

    /**
     * Returns the path to the certificate for ssl connections.
     *
     * @return string The path to cert file
     */
    public function getCertPath()
    {
        return $this->getContainer()->getContainerNode()->getReceiver()->getParam('certPath');
    }

    /**
     * Returns the passphrase for the certificate.
     *
     * @return string The path to cert file
     */
    public function getCertPassphrase()
    {
        return $this->getContainer()->getContainerNode()->getReceiver()->getParam('certPassphrase');
    }

    /**
     * @see \TechDivision\ApplicationServer\AbstractReceiver
     */
    protected function getResourceClass()
    {
        return 'TechDivision\Stream\SecureServer';
    }
}