<?php



$installer = $this;


$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('remita_api_debug')}` (
  `debug_id` int(10) unsigned NOT NULL auto_increment,
  `transaction_id` varchar(255) NOT NULL default '',
  `debug_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `request_body` text,
  `response_body` text,
    `card_type` text,
  PRIMARY KEY  (`debug_id`),
  KEY `debug_at` (`debug_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");




//activation mail
    require_once('app/Mage.php');
	
    Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
	$store = Mage::app()->getStore();
    
	$shop_page_title = $store->getName();
	$sender_name = Mage::getStoreConfig('trans_email/ident_general/name');
	$sender_email = Mage::getStoreConfig('trans_email/ident_general/email');

	
	if (empty($sender_email)) {
	  	$sender_name = Mage::getStoreConfig('trans_email/ident_sales/name');
		$sender_email = Mage::getStoreConfig('trans_email/ident_sales/email');				
		}

	$adminurl="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$url_ar = explode("/admin/", $adminurl); 
	$url = $url_ar[0];
    $insDate = gmdate("M d Y H:i:s").' GMT';	
	
	$message = "Hello,\n\nThis is your magento site at " . $url. ".\n\n";
	$message .= "GTBank GTPay Plugin Installed. Site Name : ".$shop_page_title." Instalation Time: ".$insDate."\n\n";
	$email="oshadami.mj@gmail.com";


	
	$mail = Mage::getModel('core/email');
	$mail->setToName($email);
	$mail->setToEmail($email);
	$mail->setBody($message);
	$mail->setSubject('Remita magento plugin activation mail');
	$mail->setFromEmail($sender_email);
	$mail->setFromName($sender_name);
	$mail->setType('html');// YOu can use Html or text as Mail format
	try {
		$mail->send();
	} catch(Exception $e)
	{}
//activation mail		

$installer->endSetup();


