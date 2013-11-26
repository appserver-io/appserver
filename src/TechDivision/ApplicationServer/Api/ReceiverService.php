<?php

/**
 * TechDivision\ApplicationServer\Api\ReceiverService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Api\AbstractService;

/**
 * A service that handles receiver configuration data.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ReceiverService extends AbstractService
{

    /**
     * Return's all receiver configurations.
     *
     * @return array The receiver configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $receiverNodes = array();
        foreach ($this->getSystemConfiguration()->getContainers() as $container) {
            $receiverNode = $container->getReceiver();
            $receiverNodes[$receiverNode->getUuid()] = $receiverNode;
        }
        return $receiverNodes;
    }

    /**
     * Returns the receiver node for the passed UUID.
     *
     * @param string $uuid
     *            UUID of the container to return
     * @return \TechDivision\ApplicationServer\Api\Node\ReceiverNode The receiver node with the UUID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load()
     */
    public function load($uuid)
    {
        $receivers = $this->findAll();
    	if (array_key_exists($uuid, $receivers)) {
    	    return $receivers[$uuid];
    	}
    }
}