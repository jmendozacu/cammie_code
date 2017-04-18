<?php

class Sahy_Ebay_Adminhtml_EbayController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('ebay/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
           
             $this->_initAction()
			->renderLayout();
	}
        
         public function importAction(){
            $data=Mage::app()->getRequest()->getPost();
            $sellerId=$data['seller'];
            $category=$data['category'];
            $itemid=$data['itemid'];
            $page=$data['page'];
           
            $helper = Mage::helper('ebay/item');
            
            $total=Mage::registry('total')?Mage::registry('total'):0;
            $updated=Mage::registry('updated')?Mage::registry('updated'):0;
            
            if(isset($data['import_this'])){
                $response = $helper->getSingle($itemid,$category);
                    $this->loadLayout();
                    $this->_addContent($this->getLayout()->createBlock('ebay/adminhtml_ebay'));
                    $this->renderLayout();
            }
            else{
            
            $requestXmlBody=$helper->getRequestXml($sellerId,1);
         
            $headers = $helper->EbayHeaders('findItemsAdvanced');  
          
            $response = $helper->sendHttpRequest($requestXmlBody,$headers);
          
            if(stristr($response, 'HTTP 404') || $response == ''){
                   die('<P>Error sending request');
             }
            $responseDoc = simplexml_load_string($response);
            $data_arr = $helper->objectsIntoArray($responseDoc);
           // print_r($data_arr);
             if ($data_arr['ack'] == 'Success') 
                  {
                   if (isset($data_arr['searchResult']['item'])) {
                    if (isset($data_arr['searchResult']['item'][0])){
                        $item = $data_arr['searchResult']['item'];
                    }
                    else {
                        $item = array($data_arr['searchResult']['item']);
                    }
                    $pages= $data_arr['paginationOutput']['totalPages'];
                   // echo $data_arr['searchResult']['item'];
                    $import = $helper->import_items($item,$category);
                    $total+=$import['total'];
                    $upadated+=$import['updated'];
                    Mage::register('pages',$pages);
//                    for ($x=2; $x<=1; $x++)
//                    {
//                         $requestXmlBody=$helper->getRequestXml($sellerId,$x);
//                         $response = $helper->sendHttpRequest($requestXmlBody,$headers);
//                         $responseDoc = simplexml_load_string($response);
//                         $data_arr = $helper->objectsIntoArray($responseDoc);
//                         if (isset($data_arr['searchResult']['item'][0])){
//                             $item = $data_arr['searchResult']['item'];
//                         }
//                         else {
//                             $item = array($data_arr['searchResult']['item']);
//                         }
//                         $import = $helper->import_items($item,$category);
//                         $total+=$import['total'];
//                         $upadated+=$import['updated'];
//                    } 
//                    $page=$page+1;
                    Mage::register('page', $page);
                    Mage::register('total', $total);
                    Mage::register('updated',$upadated);
                    Mage::register('seller_name',$sellerId);
                    Mage::register('category',$category);
                    if($page==$pages){
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ebay/item')->__('Successfully eBay products imported'));
                   }
                   }
                  
              }
              else{  
                  
                  echo '<pre>';
                  print_r($data_arr);
                  echo '</pre>';
                  
                   Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ebay/item')->__('Error: Something goes wrong!'));
              }
                $this->loadLayout();
                $this->_addContent($this->getLayout()->createBlock('ebay/adminhtml_ebay'));
                $this->renderLayout();
            }
        }

}