<?php
class Egs_Firstdata_IndexController extends Mage_Core_Controller_Front_Action
{
	
	public function indexAction()
	{
		ini_set('display_errors',1);
		$session = Mage::getSingleton('checkout/session');
		$orderIncrementId = $session->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		$billingaddress = $order->getBillingAddress();
		$shippingaddress = $order->getShippingAddress();
		$currencyDesc = Mage::app()->getStore()->getCurrentCurrencyCode();
		$totals =$order->getGrandTotal();
		$address = $billingaddress->getStreet();
		$address1 = $shippingaddress->getStreet();
		ini_set('display_errors',1);


		//$cc=explode("&",$order->getPayment()->getCcNumber());
		$ccnumber=Mage::getModel('core/session')->getCcnumber();
		$cvv=Mage::getModel('core/session')->getCcid();
		$encry_key=trim(Mage::getStoreConfig('payment/firstdata/encry_key'));
		
		$data = array(
		'vpc_Version' => 1,
		'vpc_Command' => 'firstdata',
		'vpc_Merchant' => trim(Mage::getStoreConfig('payment/firstdata/mer_id')),
		'vpc_AccessCode' => trim(Mage::getStoreConfig('payment/firstdata/mer_access')),
		'vpc_Amount' => $totals,
		'vpc_CardName'=>Mage::getModel('core/session')->getCcowner(),
		'vpc_Cardtypename'=>Mage::getModel('core/session')->getCctype(),
		'vpc_CardNum'=>$ccnumber,
		'vpc_CardExp'=>$this->_getcard($order),
		'vpc_Message'=>'transaction',
		'vpc_MerchTxnRef'=>'test',
		'billing_cust_name' =>$order->getCustomerFirstname(), 
		'billing_last_name'=>$order->getCustomerLastname(),
		'billing_cust_tel_No' => $billingaddress->getTelephone(),
		'billing_cust_email'=>$order->getCustomerEmail(),
		'billing_cust_address'=>$address[0] . ' '.$address[1],
		'billing_cust_city'=>$billingaddress->getCity(),
		'billing_cust_country'=>$billingaddress->getCountryId(),
		'billing_cust_state'=>$billingaddress->getRegion(),
		'billing_cust_zip' =>$billingaddress->getPostcode(),
		'Order_Id' => $order->getIncrementId()
		);
		
		
		if($cvv)
		{
		$data['vpc_CardSecurityCode']=$cvv;
		}
		$data['vpc_Currency']=$currencyDesc;
		
		$username		= trim(Mage::getStoreConfig('payment/firstdata/username'));
		$gatewayId		= trim(Mage::getStoreConfig('payment/firstdata/mer_id'));
		$gatewayPassword	= trim(Mage::getStoreConfig('payment/firstdata/mer_access'));   
		$transaction_mode	= trim(Mage::getStoreConfig('payment/firstdata/mode_version'));				
		$tran_type		= trim(Mage::getStoreConfig('payment/firstdata/trans_type'));
		$api_version		= trim(Mage::getStoreConfig('payment/firstdata/api_version'));
		$authNum		= '';
		$amount 		= str_replace(",", "", number_format($totals, 2));
		$expDate		= $data['vpc_CardExp'];
	
		$response 		= array();
		$trxnProperties 	= array(
		"User_Name"		=> $username,
		"gateway_id"		=> $gatewayId,
		"password"		=> $gatewayPassword,		  
		"Secure_AuthResult"	=> "",
		"ecommerce_flag"	=> "0",
		"xid"			=> "",		  
		"cavv"			=> "",
		"cavv_algorithm"	=> "",
		"transaction_type"	=> $tran_type, 
		"reference_no"		=> $data["Order_Id"],
		"customer_ref"		=> "",
		"client_ip"		=> $_SERVER["REMOTE_ADDR"],
		"client_email"		=> $data["billing_cust_email"],
		"language"		=> "en",
		"cc_number"		=> $data["vpc_CardNum"],
		"Card_Type"		=> $data["vpc_Cardtypename"],
		"cc_expiry"		=> $expDate,
		"cardholder_name"	=> $data["vpc_CardName"],
		"track1"		=> "",
		"track2"		=> "",
		"transaction_tag"	=> "",
		"amount"		=> $amount,
		"cc_verification_str1"	=> substr($data["billing_cust_address"], 0, 28) . "|" . $data["billing_cust_zip"],
		"cc_verification_str2"	=> $cvv,
		"cvd_presence_ind"	=> "",
		"secure_auth_required"	=> "",
		"currency_code"		=> $currencyDesc,
		"partial_redemption"	=> "",
		"zipCode"		=> $data["billing_cust_zip"],
		"tax1_amount"		=> "",
		"tax1_number"		=> "",
		"tax2_amount"		=> "",
		"tax2_number"		=> "",		  
		"surchargeamount"	=> "",	
		"pan"			=> ""		  
		);
		
		
		
		if ($transaction_mode == 'test') {
				$post_url = 'https://api.demo.globalgatewaye4.firstdata.com/transaction/v11';
			} else {
				
				$post_url = 'https://api.globalgatewaye4.firstdata.com/transaction/v11';
			}
		
		$data_string= json_encode($trxnProperties); 
		$ch = curl_init($post_url); 
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
		curl_setopt ($ch, CURLOPT_HEADER, 0); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 45); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8;','Accept: application/json' )); 
		
		// Getting results 
		$result = curl_exec($ch);
		
		if ($curl_error = curl_error ($ch)) { 
				$this->logInfo ("----CURL ERROR----\n" . $curl_error . "\n\n", 'message'); 
			} 
	 
		curl_close ($ch);
		
		//print_r($result);die;
 
 Mage::getModel('core/session')->setCmethod(null);
Mage::getModel('core/session')->setCcowner(null);
Mage::getModel('core/session')->setCctype(null);
Mage::getModel('core/session')->setCcnumber(null);
Mage::getModel('core/session')->setCcexpmonth(null);
Mage::getModel('core/session')->setCcid(null);

		// Getting jSON result string 
		$data_string = json_decode($result);
		//print_r($data_string);die;
		//return $data_string; 
		
		if($data_string->transaction_approved == 1)
		{
			$order = Mage::getModel('sales/order');
			$order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
			$order->loadByIncrementId($order_id);
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
			$order->sendNewOrderEmail();
			$order->setEmailSent(true);
			
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');

			$data_escape = (string)mysql_escape_string($data_string->ctr);
			
			$write->query("insert into firstdatagge4(order_id,transaction_type,transaction_tag,authorization_num,bank_response_code,
				      bank_response_msg,client_ip,ctr)
				      values($data_string->reference_no,'$data_string->transaction_type','$data_string->transaction_tag',
				      '$data_string->authorization_num','$data_string->bank_message',
				      '$data_string->bank_message','$data_string->client_ip','".$data_escape."')");
			
			$url = Mage::getUrl('checkout/onepage/success', array('_secure'=>true));
			Mage::register('redirect_url',$url);
			$this->_redirectUrl($url);	
		}
		else
		{
			$msg=$result;
			$this->errorAction($msg);
			
		}
	        
		
	}

	protected function _getCheckout()
	{
		return Mage::getSingleton('checkout/session');
	}

	public function errorAction($msg)
	{
		$request = $_REQUEST;
		Mage::log($request, null, 'lps.log');
		$gotoSection = false;
		$session = $this->_getCheckout();
		if ($session->getLastRealOrderId())
		{
			$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
			if ($order->getId())
			{
				//Cancel order
				if ($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED)
				{
					$order->registerCancellation($errorMsg)->save();
				}
				$quote = Mage::getModel('sales/quote')
				->load($order->getQuoteId());
				//Return quote
				if ($quote->getId())
				{
					$quote->setIsActive(1)
					->setReservedOrderId(NULL)
					->save();
					$session->replaceQuote($quote);
				}
				//Unset data
				$session->unsLastRealOrderId();
				//Redirect to payment step
				$gotoSection = 'payment';
				$url = Mage::getUrl("checkout/onepage/index?msg=$msg", array('_secure'=>true));
				Mage::register('redirect_url',$url);
				$this->_redirectUrl($url);
			}
		}
		return $gotoSection;
	}
		
	protected function _getcard($order)
	{
		$month=$order->getPayment()->getCcExpMonth();
		$year=substr($order->getPayment()->getCcExpYear(),2,2);
		if(strlen($month)==1)
		{
		$expyear="0".$month.$year;	
		}else
		{
		$expyear= $month."".$year;
		}
		return $expyear;
	}
	
		
}
