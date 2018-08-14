<?php


class Mage_Remita_Block_Standard_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('remita/standard/form.phtml');
        parent::_construct();
		
    }
	
   public function getConfig()
    {
        return Mage::getSingleton('remita/config');
    }
	
	public function getAvailablePaymentTypes()
	{
		return Mage::getSingleton('remita/paymentoptions')->getCollection();
	}
			
			
		public function getAvailableTypes()
    {
     
            $availableTypes = $this->getConfig()->getpayment_options();
		     if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                          
        }
        return $availableTypes;
    }
	
	
	
	  public function getEnabledPaymentTypes()
    {
        $types = $this->getAvailablePaymentTypes();
        if ($method = $this->getMethod()) {
            $availableTypes = $this->getConfig()->getpayment_options();
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;
    }
}