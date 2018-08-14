<?php

class Mage_Remita_Model_Mysql4_Api_Debug extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('remita/api_debug', 'transaction_id');
    }
}