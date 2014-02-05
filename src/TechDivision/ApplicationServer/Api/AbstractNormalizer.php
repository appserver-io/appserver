<?php
/**
 * TechDivision\ApplicationServer\AbstractApplication
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

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\NormalizerInterface;
use TechDivision\ApplicationServer\Api\ServiceInterface;

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
abstract class AbstractNormalizer implements NormalizerInterface
{

    /**
     * The initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext;
     */
    protected $initialContext;

    /**
     * Initializes the normalizer with the initial context.
     *
     * @param InitialContext   $initialContext The initial context instance
     * @param ServiceInterface $service        The service to normalize for
     */
    public function __construct(InitialContext $initialContext, ServiceInterface $service)
    {
        $this->initialContext = $initialContext;
        $this->service = $service;
    }

    /**
     * (non-PHPdoc)
     *
     * @return InitialContext
     * @see NormalizerInterface::getInitialContext()
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * (non-PHPdoc)
     *
     * @return ServiceInterface
     * @see NormalizerInterface::getService()
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return InitialContext
     * @see InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }
}
