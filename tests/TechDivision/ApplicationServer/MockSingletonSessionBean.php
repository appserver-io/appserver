<?php

namespace TechDivision\ApplicationServer;

/**
 * @Singleton
 */
class MockSingletonSessionBean {
    
    protected $persistentValue;
    
    public function setPersistentValue($persistentValue) {
        $this->persistentValue = $persistentValue;
    }
    
    public function getPersistentValue() {
        return $this->persistentValue;
    }
}