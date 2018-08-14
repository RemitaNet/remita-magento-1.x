<?php

class Mage_Remita_Model_Config extends Varien_Object
{
    const MODE_TEST         = 'TEST';
    const MODE_LIVE         = 'LIVE';

    const PAYMENT_TYPE_PAYMENT      = 'PAYMENT';
    const PAYMENT_TYPE_DEFERRED     = 'DEFERRED';
    const PAYMENT_TYPE_AUTHENTICATE = 'AUTHENTICATE';
    const PAYMENT_TYPE_AUTHORISE    = 'AUTHORISE';


  
    public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
             $value = Mage::getStoreConfig('payment/remita_standard/'.$key);
             if (is_null($value) || false===$value) {
                 $value = $default;
             }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

	public function getDescription ()
    {
        return $this->getConfigData('description');
    }

    public function getmerchant_id ()
    {
        return $this->getConfigData('merchant_id');
    }
    public function getservicetype_id ()
    {
        return $this->getConfigData('servicetype_id');
    } 
    public function getapi_key ()
    {
        return $this->getConfigData('api_key');
    }

	public function getgateway_destination ()
    {
        return $this->getConfigData('gateway_destination');
    }
	public function getpayment_options ()
    {
        return $this->getConfigData('payment_options');
    }

	
    public function getMode ()
    {
        return $this->getConfigData('environment');
    }

  
    public function getNewOrderStatus ()
    {
        return $this->getConfigData('order_status');
    }

  
    public function getDebug ()
    {
        return $this->getConfigData('debug_flag');
    }




}