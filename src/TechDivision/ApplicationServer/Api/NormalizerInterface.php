<?php
/**
 * TechDivision\ApplicationServer\Api\NormalizerInterface
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api;

use TechDivision\Configuration\Interfaces\ConfigurationInterface;

/**
 * Interface for all normalizers.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface NormalizerInterface
{

    /**
     * Normalizes the passed configuration node and returns a \stdClass
     * representation of it.
     *
     * @param \TechDivision\Configuration\Interfaces\ConfigurationInterface $configuration The configuration node to normalize
     *
     * @return \stdClass The normalized configuration node
     */
    public function normalize(ConfigurationInterface $configuration);

    /**
     * Returns the initial context instance.
     *
     * @return \TechDivision\Application\Interfaces\ContextInterface The initial Context
     */
    public function getInitialContext();

    /**
     * Return the service to normalize for.
     *
     * @return ServiceInterface The service instance
     */
    public function getService();
}
