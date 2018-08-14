<?php

class Mage_Remita_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'remita_standard';
    protected $_formBlockType = 'remita/standard_form';
    protected $_infoBlockType = 'remita/standard_info';
	
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_order = null;


    public function getConfig()
    {
        return Mage::getSingleton('remita/config');
    }


	public function getInfoInstance()
	{
		$payment = $this->getData('info_instance');
		if (! $payment)
		{
			$payment = $this->getOrder()->getPayment();
			$this->setInfoInstance($payment);
		}
		return $payment;
	}

	

	public function get_PaymentInfoData($key, $payment = null)
	{
        if (is_null($payment))
		{
			$timesammp=DATE("dmyHis");		
		    $transactionId = $timesammp;
			$payment = $this->getInfoInstance();
    		$payment->setAdditionalInformation('transaction_id', $transactionId);			
			$payment->save;
		}
		return $payment->getAdditionalInformation($key);
	}	
	

	public function get_TransactionId()
	{
		return $this->get_PaymentInfoData('transaction_id');
	}	
	

    public function assignData($data)
    {
        $result = parent::assignData($data);     
   		 $details = array();   
    	 $details['Transaction_Id'] = $this->get_TransactionId();
         if (!empty($details)) {
            $this->getInfoInstance()->setAdditionalData(serialize($details));
         }		 
        return $result;
    }	
  
    public function getDebug ()
    {
        return $this->getConfig()->getDebug();
    }

   
    public function getRemitaUrl ()
    {
        switch ($this->getConfig()->getMode()) {
            case Mage_Remita_Model_Config::MODE_LIVE:
              $url = 'https://login.remita.net/remita/ecomm/init.reg';
                break;
            case Mage_Remita_Model_Config::MODE_TEST:
                   $url =  "http://www.remitademo.net/remita/ecomm/init.reg";
                break;
            default: 
                $url = 'https://login.remita.net/remita/ecomm/init.reg';
                break;
        }
        return $url;
    }
	
    protected function getSuccessURL ()
    {
        return Mage::getUrl('remita/standard/successresponse');
    }

  
    protected function getFailureURL ()
    {
        return Mage::getUrl('remita/standard/failureresponse');
    }

 
    protected function getVendorTxCode ()
    {
        return $this->getOrder()->getRealOrderId();
    }

  
    protected function getFormattedCart ()
    {
        $items = $this->getOrder()->getAllItems();
        $resultParts = array();
        $totalLines = 0;
        if ($items) {
            foreach($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $quantity = $item->getQtyOrdered();

                $cost = sprintf('%.2f', $item->getBasePrice() - $item->getBaseDiscountAmount());
                $tax = sprintf('%.2f', $item->getBaseTaxAmount());
                $costPlusTax = sprintf('%.2f', $cost + $tax/$quantity);

                $totalCostPlusTax = sprintf('%.2f', $quantity * $cost + $tax);

                $resultParts[] = str_replace(':', ' ', $item->getName());
                $resultParts[] = $quantity;
                $resultParts[] = $cost;
                $resultParts[] = $tax;
                $resultParts[] = $costPlusTax;
                $resultParts[] = $totalCostPlusTax;
                $totalLines++; 
            }
       }

       // add delivery
       $shipping = $this->getOrder()->getBaseShippingAmount();
       if ((int)$shipping > 0) {
           $totalLines++;
           $resultParts = array_merge($resultParts, array('Shipping','','','','',sprintf('%.2f', $shipping)));
       }

       $result = $totalLines . ':' . implode(':', $resultParts);
       return $result;
    }


  
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('remita/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());
        return $block;
    }


    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('remita/standard/redirect');
    }    

  
    public function getStandardCheckoutFormFields ()
    {
        $order = $this->getOrder();		
        $amount = $order->getBaseGrandTotal();
		$amount = intval($amount);
        $description = Mage::app()->getStore()->getName() . ' ' . ' payment';
		$transactionId = $this->get_PaymentInfoData('transaction_id', $order->getPayment());
			
		 $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('remita')->__('Remita Order ID :'.$transactionId)
        );
		
        $order->save();		
        $billing = $order->getBillingAddress();		
		$cust_id = $order->getCustomerId();
		$payment = $order->getPayment();
		$card_type = $payment->getData('cc_type');
		$cust_name = $billing->getFirstname().' '.$billing->getLastname();
		$mert_id = $this->getConfig()->getmerchant_id();
        $serv_id = $this->getConfig()->getservicetype_id();
        $api_key = $this->getConfig()->getapi_key();
		$gateway_destination = $this->getConfig()->getgateway_destination();
		$payment_options = $this->getConfig()->getpayment_options();
	    $hash_string = $mert_id . $serv_id . $transactionId . $amount . $this->getSuccessURL() . $api_key;
		$hash = hash('sha512', $hash_string);
		$cust_email = $billing->getEmail();
				
        $fields = array(
		'merchantId' => $mert_id,														
        'serviceTypeId'  => $serv_id,
        'amt' => $amount,
        'responseurl' => $this->getSuccessURL(),																		
        'hash' => $hash,			
		'paymenttype' => $card_type,	
        'payerName' => $cust_name,												
        'payerEmail' => $cust_email,	
        'orderId' => $transactionId,																	
                        );
        return $fields;
    }
}