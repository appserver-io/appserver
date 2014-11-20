<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\AppNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer an app.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
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
}
