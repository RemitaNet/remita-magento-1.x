<?php


class Mage_Remita_Block_Standard_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $standard = Mage::getModel('remita/standard');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getRemitaUrl())
            ->setId('remita_standard_checkout')
            ->setName('remita_standard_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        $html = '<html><body>
        ';
        $html.= $this->__('You will be redirected to Remita in few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("remita_standard_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}