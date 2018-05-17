<?php

/**
 * \AppserverIo\Appserver\Core\Api\NormalizerInterface
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

use AppserverIo\Configuration\Interfaces\ConfigurationInterface;

/**
 * Interface for all normalizers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface NormalizerInterface
{

    /**
     * Normalizes the passed configuration node and returns a \stdClass
     * representation of it.
     *
     * @param \AppserverIo\Configuration\Interfaces\ConfigurationInterface $configuration The configuration node to normalize
     *
     * @return \stdClass The normalized configuration node
     */
    public function normalize(ConfigurationInterface $configuration);

    /**
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Psr\ApplicationServer\ContextInterface The initial Context
     */
    public function getInitialContext();

    /**
     * Return the service to normalize for.
     *
     * @return \AppserverIo\Psr\ApplicationServer\ServiceInterface The service instance
     */
    public function getService();
}
