<?php
/**
 * TechDivision\ApplicationServer\Api\DtoNormalizer
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

use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\Api\NormalizerInterface;

/**
 * Normalizes configuration nodes to DTO instances.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DtoNormalizer extends AbstractNormalizer
{

    /**
     * (non-PHPdoc)
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration The configuration node to normalize
     *
     * @return \stdClass The normalized configuration node
     * @see \TechDivision\ApplicationServer\Api\NormalizerInterface::normalize()
     */
    public function normalize(Configuration $configuration)
    {
        $nodeType = $this->getService()->getNodeType();
        return $this->newInstance($nodeType, array(
            $configuration
        ));
    }
}
