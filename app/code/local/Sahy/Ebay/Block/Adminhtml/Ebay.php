
<?php

 

class sahy_ebay_Block_Adminhtml_ebay extends Mage_Adminhtml_Block_Template
{
  public function __construct()
  {
       parent::__construct();
       $cat=$this->helper('ebay/item')->getCategories();
       //$cat2= array('1','2');
       $this->setData('cats',$cat);
       $this->setTemplate('ebay/ebay.phtml');
  }
}