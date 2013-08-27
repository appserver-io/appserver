<?php

namespace TechDivision\ApplicationServer;

/**
 * @Stateful
 */
class MockStatefulSessionBean {
    
    protected $persistentValue;
    
    public function setPersistentValue($persistentValue) {
        $this->persistentValue = $persistentValue;
    }
    
    public function getPersistentValue() {
        return $this->persistentValue;
    }
}