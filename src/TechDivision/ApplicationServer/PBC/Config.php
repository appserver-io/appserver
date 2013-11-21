<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wickb
 * Date: 26.06.13
 * Time: 09:44
 * To change this template use File | Settings | File Templates.
 */

namespace TechDivision\ApplicationServer\PBC;

use TechDivision\PBC\Interfaces\PBCConfig;
use Psr\Log\LoggerInterface;

class Config implements PBCConfig
{
    public function __construct()
    {
        /**
         * Specify which classes are affected by the library's autoloading process.
         *
         * 'omit' lets you specify namespaces which are not parsed or check for contracts.
         *
         * 'projectRoot' specifies the root of your project. All enclosed php structures such as classes, interfaces
         * and traits will be included in the autoload and parsing process.
         */
        $this->config['AutoLoader'] = array(
            'omit' => array('TechDivision\PBC', 'PHPUnit', 'PHPParser', 'Symfony\Component', 'Psr\Log'),
            'projectRoot' => array('/opt/appserver/app/code/vendor/techdivision',
                                   '/opt/appserver/webapps')
        );

        /**
         * Specify how and what to enforce in therms of contracts.
         *
         * 'enforceDefaultTypeSafety' (true|false) states if type hints with @param and @return should be considered
         *      pre- or postconditions in terms of a variable type check
         *
         * 'processing' ('exception'|'logging'|'none') states how the library should react in case of a contract
         *      violation. If 'logging' is chosen, the config entry 'logger' has to be filled correctly.
         *
         * 'logger' specify a logging class using its fully qualified name. The class has to be PSR-3 compliant and
         *      should therefore implement the Psr\Log\LoggerInterface interface which comes with this library.
         *      (See also https://github.com/php-fig/log)
         *      If the specified class does not satisfy our needs 'processing' will default to 'none'
         */
        $this->config['Enforcement'] = array(
            'enforceDefaultTypeSafety' => false,
            'processing' => 'exception',
            'logger' => ''
        );

        /**
         * Here you can specify the environment in which php-by-contract operates.
         * See possible options below.
         *
         * 'development' will omit caching of structures.
         *
         * 'production' will utilize the full potential and omit error output others than
         * specified in 'Enforcement''processing'.
         */
        $this->config['Environment'] = 'production';


        // Validate the configuration.
        $this->validate();
    }

    /**
     *
     */
    private function validate()
    {
        // Check if we have to use a logger, and if so check if it complies with PSR-3.
        if ($this->config['Enforcement']['processing'] === 'logging') {

            // Instantiate our logger candidate
            $loggerCandidate = $this->config['Enforcement']['logger'];
            $loggerInterfaces = class_implements($loggerCandidate);

            // Does it implement the PSR-3 interface?
            if (!isset($loggerInterfaces['Psr\Log\LoggerInterface'])) {

                // Logger does not satisfy PSR-3, lets set processing to none
                $this->config['Enforcement']['processing'] = 'none';
            }
        }

        // There was no error till now, so return true.
        return true;
    }

    /**
     * @param string $aspect
     *
     * @return array
     */
    public function getConfig($aspect = null)
    {
        if (!is_null($aspect) && isset($this->config[$aspect])) {

            return $this->config[$aspect];

        } else {

            return $this->config;
        }
    }
}

