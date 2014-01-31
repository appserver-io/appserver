<?php
/**
 * TechDivision\ApplicationServer\Api\Normalizer
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

/**
 * Normalizes configuration nodes to \stdClass instances.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class Normalizer extends AbstractNormalizer
{

    /**
     * Normalizes the passed configuration node and returns a \stdClass
     * representation of it.
     *
     * @param Configuration $configuration The configuration node to normalize
     *
     * @return \stdClass The normalized configuration node
     */
    public function normalize(Configuration $configuration)
    {
        // initialize the \stdClass instance
        $node = $this->newInstance('\stdClass');
        $node->{$configuration->getNodeName()} = new \stdClass();

        // set the node value if available
        if ($value = $configuration->getValue()) {
            $node->{$configuration->getNodeName()}->value = $value;
        }

        // set members by converting camel case to underscore (necessary for ember.js)
        foreach ($configuration->getAllData() as $member => $value) {
            $node->{$configuration->getNodeName()}->{strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $member))} = $value;
        }

        // return the normalized node instance
        return $node;
    }
}
