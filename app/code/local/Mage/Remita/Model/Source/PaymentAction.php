<?php

class Mage_Remita_Model_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Remita_Model_Config::PAYMENT_TYPE_PAYMENT, 'label' => Mage::helper('remita')->__('PAYMENT')),
            array('value' => Mage_Remita_Model_Config::PAYMENT_TYPE_DEFERRED, 'label' => Mage::helper('remita')->__('DEFERRED')),
            array('value' => Mage_Remita_Model_Config::PAYMENT_TYPE_AUTHENTICATE, 'label' => Mage::helper('remita')->__('AUTHENTICATE')),
        );
    }
}