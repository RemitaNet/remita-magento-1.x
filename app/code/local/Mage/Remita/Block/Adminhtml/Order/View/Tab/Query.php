<?php

class Mage_Remita_Block_Adminhtml_Order_View_Tab_Query
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_chat = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('remita/order/view/tab/query.phtml');
    }

    public function getTabLabel() {
        return $this->__('Remita Check Status Tool');
    }

    public function getTabTitle() {
        return $this->__('Remita Query Tool');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getOrder(){
        return Mage::registry('current_order');
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('remita/standard/query?order_id='.$this->getOrder()->getRealOrderId().'&otv=');
    }
    public function getonclick(){
      $onclick = "submitAndReloadArea($('sales_order_view_tabs_adminhtml_order_view_tab_query_val').parentNode, '".$this->getSubmitUrl()."')";	
  	  return $onclick;
	}

	
    public function getOrder_TransactionId(){
		$order = $this->getOrder();
		
        return $order->getPayment()->getAdditionalInformation('transaction_id');
    }	

	
}