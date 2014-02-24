<?php
/**
 * TechDivision\ApplicationServer\Api\Node\AppNode
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

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer an app.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class AppNode extends AbstractNode
{

    /**
     * The unique application name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The application's path.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $webappPath;

    /**
     * The application's datasources (if any).
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\DatasourceNode>
     * @AS\Mapping(nodeType="array")
     */
    protected $datasources;

    /**
     * Set's the application name.
     *
     * @param string $name The unique application name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return's the application name.
     *
     * @return string The unique application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set's the application's path.
     *
     * @param string $webappPath The application's path
     *
     * @return void
     */
    public function setWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Return's the application's path.
     *
     * @return string The application's path
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Sets the list of datasources for this app.
     *
     * @param array<\TechDivision\ApplicationServer\Api\Node\DatasourceNode> $datasources The list of datasources
     *                                                                                    for this app
     *
     * @return void
     */
    public function setDatasources(array $datasources)
    {
        $this->datasources = $datasources;
    }

    /**
     * Returns the list of datasources for this app.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\DatasourceNode> The list of datasources for this app
     */
    public function getDatasources()
    {
        return $this->datasources;
    }
}
