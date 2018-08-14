<?php

class Mage_Remita_Model_Paymentoptions
{
		private $paymentOptions = array(  
			REMITA_PAY => "Remita Account Transfer",  
			Interswitch => "Verve Card",  
			UPL => "Visa",  
			MasterCard => "MasterCard",  
			PocketMoni => "PocketMoni",
			BANK_BRANCH => "Bank Branch",
			BANK_INTERNET => "Internet Banking",
			ATM =>"ATM",
            //Add more static Payment option here...  
        );  
      
        public function getCollection()  
        {  
            return $this->paymentOptions;  
        }  
      
        public function toOptionArray()  
        {  
            $arr = array();  
            foreach ($this->paymentOptions as $key => $val) {  
                $arr[] = array(  
                    "value" => $key,  
                    "label" => $val,  
                );  
            }  
            return $arr;  
        }  
    
}

