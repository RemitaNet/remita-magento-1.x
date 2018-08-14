<?php

class Mage_Remita_StandardController extends Mage_Core_Controller_Front_Action
{
    public $isValidResponse = false;

   
    public function getStandard()
    {
        return Mage::getSingleton('remita/standard');
    }

   
    public function getConfig()
    {
        return $this->getStandard()->getConfig();
    }

 
    public function getRemitaIPNUrl ()
    {
        switch ($this->getConfig()->getMode()) {
            case Mage_Remita_Model_Config::MODE_LIVE:
                $url = 'https://login.remita.net/remita/ecomm';
                break;
            case Mage_Remita_Model_Config::MODE_TEST:
                   $url =  "http://www.remitademo.net/remita/ecomm";
                break;
            default: 
                $url = 'https://login.remita.net/remita/ecomm';
                break;
        }
        return $url;
    }	
		public function updatePaymentStatus($transactionId,$response_code,$response_reason,$rrr)	{
			switch($response_code)
			{
				case "00":                    
				   if ($this->getDebug()) {Mage::getModel('remita/api_debug')->setResponseBody(print_r($this->responseArr,1))->save();}
					$order = Mage::getModel('sales/order');
					$order->loadByIncrementId($transactionId);
					if (!$order->getId()) {
						return false;
					}
					$order->addStatusToHistory($order->getStatus(), Mage::helper('remita')->__('Payment Received. Remita Retrieval Reference :'.$rrr.''));
					$order->getPayment()->setTransactionId($rrr);
					$order->getPayment()->setAdditionalInformation('transaction_id', $rrr);
					if ($this->saveInvoice($order)) {
						$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
					} else {
						$newOrderStatus = $this->getConfig()->getNewOrderStatus() ?
							$this->getConfig()->getNewOrderStatus() : Mage_Sales_Model_Order::STATE_NEW;
					}
					$order->save();
					$order->sendNewOrderEmail();
					$pagetitle = "Remita - Payment Confirmation";
					$subtitle  = "Thank you, we have received your payment!";					
					$msg .=  '<br><b>Remita Retrieval Reference: <b>'.$rrr;			
					$redirectTo = 'remita/standard/success';
				break;
				case "01":                    
				   if ($this->getDebug()) {Mage::getModel('remita/api_debug')->setResponseBody(print_r($this->responseArr,1))->save();}
					$order = Mage::getModel('sales/order');
					$order->loadByIncrementId($transactionId);
					if (!$order->getId()) {
						return false;
					}
					$order->addStatusToHistory($order->getStatus(), Mage::helper('remita')->__('Payment Received. Remita Retrieval Reference :'.$rrr.''));
					$order->getPayment()->setTransactionId($rrr);
					$order->getPayment()->setAdditionalInformation('transaction_id', $rrr);
					if ($this->saveInvoice($order)) {
						$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
					} else {
						$newOrderStatus = $this->getConfig()->getNewOrderStatus() ?
							$this->getConfig()->getNewOrderStatus() : Mage_Sales_Model_Order::STATE_NEW;
					}
					$order->save();
					$order->sendNewOrderEmail();		
					$pagetitle = "Remita - Payment Confirmation";
					$subtitle  = "Thank you, we have received your payment!";
					$msg .=  '<br><b>Remita Retrieval Reference: <b>'.$rrr;			
					$redirectTo = 'remita/standard/success';
				break;
				case "021":                    
				   if ($this->getDebug()) {Mage::getModel('remita/api_debug')->setResponseBody(print_r($this->responseArr,1))->save();}
					$order = Mage::getModel('sales/order');
					$order->loadByIncrementId($transactionId);
					if (!$order->getId()) {
						return false;
					}
					$order->addStatusToHistory($order->getStatus(), Mage::helper('remita')->__('Payment Pending, RRR Generated Successfully. Remita Retrieval Reference :'.$rrr.''));
					$order->getPayment()->setTransactionId($rrr);
					$order->getPayment()->setAdditionalInformation('transaction_id', $rrr);
					$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
					$order->save();
					$pagetitle = "Remita - RRR Generation Confirmation";
					$subtitle  = "Thank you for shopping with us. RRR Generated Successfully, You can make payment for the RRR by visiting the nearest ATM or POS!";
					$msg .=  '<br><b>Remita Retrieval Reference: <b>'.$rrr;			
					$redirectTo = 'remita/standard/success';
				break;
				default:
					$order = Mage::getModel('sales/order');
					$order->loadByIncrementId($transactionId);
					$order->getPayment()->setTransactionId($rrr);
					$order->getPayment()->setAdditionalInformation('transaction_id', $rrr);
					if (!$order->getId()) {	return false;}
					$order->addStatusToHistory(
					$order->getStatus(),Mage::helper('remita')->__('Payment Failed. Remita Retrieval Reference :'.$rrr.', Error Message :'.$response_reason));
					$order->save();
					$pagetitle = "";
					$subtitle  = "";
					$msg = '<b>Reason: <b>'.$response_reason;	
					$msg .=  '<br><b>Remita Retrieval Reference: <b>'.$rrr;
					$history = Mage::helper('remita')->__($msg);
					$redirectTo = 'remita/standard/failure';
					
				break;
			}
			return array($msg, $redirectTo,$pagetitle,$subtitle);
		}
	 public function remita_transaction_details($orderId){
				$query_url = $this->getRemitaIPNUrl();
				$mert = $this->getConfig()->getmerchant_id();
                $api_key = $this->getConfig()->getapi_key();
				$hash_string = $orderId . $api_key . $mert;
				$hash = hash('sha512', $hash_string);
				$url 	= $query_url . '/' . $mert  . '/' . $orderId . '/' . $hash . '/' . 'orderstatus.reg';
				//  Initiate curl
				$ch = curl_init();
				// Disable SSL verification
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				// Will return the response, if false it print the response
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// Set the url
				curl_setopt($ch, CURLOPT_URL,$url);
				// Execute
				$result=curl_exec($ch);
				// Closing
				curl_close($ch);
				$response = json_decode($result, true);
				return $response;
			}
    public function getDebug ()
    {
        return $this->getStandard()->getDebug();
    }

   
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setRemitaStandardQuoteId($session->getQuoteId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('remita')->__('Customer was redirected to Remita')
        );
        $order->save();

        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('remita/standard_redirect')
                ->setOrder($order)
                ->toHtml());

        $session->unsQuoteId();
    }

  
    public function  successResponseAction()
    {
        $this->preResponse();

        if (!$this->isValidResponse) {
            $this->_redirect('');
            return ;
        }
		$orderId = $this->getRequest()->getParam('orderID');
		$transactionId = $this->responseArr['transactionId'];
		$response =  $this->remita_transaction_details($orderId);
		$response_code = $response['status'];
		$rrr = $response['RRR'];
		$response_reason = $response['message'];
		$callUpdate = $this->updatePaymentStatus($transactionId,$response_code,$response_reason,$rrr);
		$message = $callUpdate[0];
		$redirectTo = $callUpdate[1];
		$titlemessage = $callUpdate[2];
		$subtitlemessage = $callUpdate[3];
	 	$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getRemitaStandardQuoteId(true));
		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
		$session->setErrorMessage($message);
		$session->setTitleMessage($titlemessage);
		$session->setSubTitleMessage($subtitlemessage);
		$this->_redirect($redirectTo);	
    }

    protected function saveInvoice (Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();

            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
            return true;
        }

        return false;
    }


    
    protected function preResponse ()
    {
    	$IPNurl = $this->getRemitaIPNUrl();
        $this->responseArr = $_GET;    

        $session = Mage::getSingleton('checkout/session');
        $this->responseArr['transactionId'] = $session->getLastRealOrderId();		

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->responseArr['transactionId']);
		   
       $this->isValidResponse = true;
        
    }

  
    public function failureAction ()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setRemitaStandardQuoteId($session->getQuoteId());
		
        if (!$session->getErrorMessage()) {
            $this->_redirect('checkout/cart');
            return;
        }
		
        $this->loadLayout();
        $this->_initLayoutMessages('remita/session');
        $this->renderLayout();
    }
	

     public function successAction ()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setRemitaStandardQuoteId($session->getQuoteId());
		
        if (!$session->getErrorMessage()) {
            $this->_redirect('checkout/cart');
            return;
        }
		 if (!$session->getTitleMessage()) {
            $this->_redirect('checkout/cart');
            return;
        }
		 if (!$session->getSubTitleMessage()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('remita/session');
        $this->renderLayout();

    }
	   /* * Remita Payment Notification
         */
		public function remitanotificationAction() {
		$json = file_get_contents('php://input');
		$arr=json_decode($json,true);
		try {
		if($arr!=null){
			foreach($arr as $key => $orderArray){
				$orderRef = $orderArray['orderRef'];			
				$response =  $this->remita_transaction_details($orderRef);
				$response_code = $response['status'];
				$rrr = $response['RRR'];
				$response_reason = $response['message'];
				$callUpdate = $this->updatePaymentStatus($orderRef,$response_code,$response_reason,$rrr);	
				}
	
		}
		exit('OK');
		}
		catch (Exception $e) {
				exit('Error Updating Notification: ' . $e);
			}	
			}
     public function queryAction ()
    {
    	$json_url = $this->getRemitaIPNUrl();
        $this->responseArr = $_GET;    

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->responseArr['order_id']);
		$amount = $order->getBaseGrandTotal();
		$amount = intval($amount);
		$trans_no = $order->getPayment()->getAdditionalInformation('transaction_id');
		$mert_id = $this->getConfig()->getmerchant_id();
        $serv_id = $this->getConfig()->getservicetype_id();
        $api_key = $this->getConfig()->getapi_key();
		$hash_string = $trans_no . $api_key . $mert_id;
		$hash = hash('sha512', $hash_string);	
		$json_url .= '/' . $mert_id . '/' . $trans_no. '/' . $hash. '/' . 'status.reg';
			$ch = curl_init( $json_url );
			$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
				);

		
			curl_setopt_array( $ch, $options );

		
			$result = curl_exec($ch);
			$resAr = json_decode($result, true);
            if (empty($result)) {
        		
                $result =  "Unable to Connect Server";
				
				}
			else {
			
			$RRR = $resAr['paymentref'];
			$message = $resAr['message'];
			$TransactionDate = $resAr['transactiontime'];
			echo "<div id='sales_order_view_tabs_adminhtml_order_view_tab_query_val'>";
			echo "<b>Status: </b>".$message.'<br>';			
			echo "<b>Transaction Date: </b>".$TransactionDate.'<br>';									
			echo "</div>";
			}


    }	
	
	
 	
	
 			
}