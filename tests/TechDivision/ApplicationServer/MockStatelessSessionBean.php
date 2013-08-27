<?php

namespace TechDivision\ApplicationServer;

/**
 * @Stateless
 */
class MockStatelessSessionBean {
    
    protected $aValue;
    
    public function setAValue($aValue) {
        $this->aValue = $aValue;
    }
    
    public function getAValue() {
        return $this->aValue;
    }
}