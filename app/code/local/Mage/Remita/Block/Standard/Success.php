<?php


class Mage_Remita_Block_Standard_Success extends Mage_Core_Block_Template
{
     public function getSuccessMessage ()
    {
        $error  = Mage::getSingleton('checkout/session')->getErrorMessage();
        Mage::getSingleton('checkout/session')->unsErrorMessage();
        return $error;
    }
	 public function getTitleMessage ()
    {
        $title  = Mage::getSingleton('checkout/session')->getTitleMessage();
        Mage::getSingleton('checkout/session')->unsTitleMessage();
        return $title;
    }
	 public function getSubTitleMessage ()
    {
        $subtitle  = Mage::getSingleton('checkout/session')->getSubTitleMessage();
        Mage::getSingleton('checkout/session')->unsSubTitleMessage();
        return $subtitle;
    }
    /**
     * Get continue shopping url
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}