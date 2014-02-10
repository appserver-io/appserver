<?php
/**
 * TechDivision\ApplicationServer\AbstractContainer
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Utilities\StateKeys;

/**
 * An abstraction context layer for Threads.
 * It will automatically register the intitialContext object.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Johann Zelger <jz@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
abstract class AbstractContextThread extends AbstractThread
{
    /**
     * Holds the initialContext object
     *
     * @var \Stackable
     */
    public $initialContext;
    
    /**
     * The server's stop state.
     * 
     * @var \TechDivision\ApplicationServer\Utilities\StateKeys
     */
    protected $stopState;

    /**
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @param \Stackable $initialContext The initial context instance
     */
    public function __construct($initialContext)
    {
        // initialize the server's stop state
        $this->stopState = StateKeys::get(StateKeys::STOPPING);
        // get function args
        $functionArgs = func_get_args();
        // shift first arg to initialContext which should be a stackable implementation.
        $this->initialContext = array_shift($functionArgs);
        // call parent
        call_user_func_array(array('parent', '__construct'), $functionArgs);
    }
    
    /**
     * (non-PHPdoc)
     *
     * @return void
     * @see \TechDivision\ApplicationServer\AbstractThread::run()
     */
    public function run()
    {
        // register the class loader again, because in a Thread the context has been lost maybe
        $this->getInitialContext()->getClassLoader()->register(true);

        // call the parent run method to start the thread
        parent::run();
    }

    /**
     * Returns the initialContext object
     *
     * @return \Stackable
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }
    
    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     * 
     * @param string $className The class name to create the instance of
     * @param array  $args      The parameters to pass to the constructor
     *
     * @return object The created instance
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }
    
    /**
     * Returns the server's stop state.
     * 
     * @return \TechDivision\ApplicationServer\Utilities\StateKeys The stop state
     */
    public function getStopState()
    {
        return $this->stopState;
    }
    
    /**
     * Returns TRUE if the appserver sends the shudown flag else FALSE.
     * 
     * @return boolean TRUE if the server has to be shutdown, else FALSE
     */
    public function shutdown()
    {
        return $this->getStopState()->equals(
            $this->getInitialContext()->getAttribute(StateKeys::KEY)
        );
    }
}
