<?php

class Mage_Remita_Model_Source_ModeAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Remita_Model_Config::MODE_LIVE, 'label' => Mage::helper('remita')->__('Live')),
            array('value' => Mage_Remita_Model_Config::MODE_TEST, 'label' => Mage::helper('remita')->__('Test')),			
        );
    }
}



