<?php
class sahy_ebay_Helper_Item extends Mage_Core_Helper_Abstract
{  
    public function getRequestXml($seller,$page){
        //echo 'get request xml called with page-->'.$page.'<br />';
           $requestXml= '<?xml version="1.0" encoding="UTF-8"?>
                <findItemsAdvancedRequest xmlns="http://www.ebay.com/marketplace/search/v1/services">
                 <keywords></keywords>
                <itemFilter>
                <name>Seller</name>
                <value>'.trim($seller).'</value>
                </itemFilter>
                <itemFilter>
                <name>ListingType</name>
                <value>FixedPrice</value>
                </itemFilter>
                <itemFilter>
                <name>HideDuplicateItems</name>
                <value>true</value>
                </itemFilter>
                <paginationInput>
                <pageNumber>1</pageNumber>
                <entriesPerPage>10</entriesPerPage>
                </paginationInput>
                <outputSelector>PictureURLSuperSize</outputSelector>
                <outputSelector>PictureURLLarge</outputSelector>
                
                </findItemsAdvancedRequest>';  
           return $requestXml; 
    }

    public function additem($id,$price,$name,$desc,$sku,$path,$qty,$category_id,$name_value,$days,$item_url,$total,$options,$option_label){
                    $pro  = Mage::getModel('catalog/product');

                    foreach($name_value as $nv){
                        $pro->setData($nv[0],$nv[1]);
                    }
              //     echo 'add item called';
                    
                     /*--------------variation start-------------*/
                    $i=0;
                    foreach($options as $op){
                    $option_list_[$i] = array(
                    'title' => $option_label_[$i],
                    'type' => 'drop_down', // could be drop_down ,checkbox , multiple
                    'is_require' => 1,
                    'sort_order' => 0,
                    'values' => $this->getOptions($op)
                    );
                    $i++;
                    }
                    $pro->setProductOptions($option_list_);
                    $pro->setCanSaveCustomOptions(true);
                      /*--------------variation end-------------*/
                    
                    
                    $count=0;
                   foreach ($path as $p) :
                       if ($count == 0) :
                            $pro->addImageToMediaGallery($p, array('thumbnail','image','small_image'), false); 
                        else :
                             $pro->addImageToMediaGallery($p,null, false, false );
                        endif;
                            $count++;  
                    endforeach; 
                     
                    $pro->setType('Simple Product');
                    $pro->setWebsiteIds(array(1));
                    $pro->setSku($id);
                    $pro->setPrice($price);
                    $pro->setCategoryIds(array($category_id));
                    $pro->setAttributeSetId(Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId());
                    $pro->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
                    $pro->setTypeId('simple');
                    $pro->setName($name);
                    $pro->setDescription($desc);
                    $pro->setShortDescription($name);
                    $pro->setstatus(1);
                    $pro->setTaxClassId(0);
                    $pro->setWeight(0);
                    $pro->setStockData(array(
                     'is_in_stock' => 1,
                     'qty' => 1
                    ));
                    $pro->setCreatedAt(strtotime('now'));
//                    $pro->setVendorHttpLink($name);
//                    $pro->setVendorSku($id);//ebay item id
//                    $pro->setVendorPrice($total);//total price
//                    $pro->setVendorShippingPrice($item_url);//item url
//                    $pro->setTotalCost($days);
                   
                   // $pro->save();
                    $pro->getResource()->save($pro); 
            }
            
    public function getCategories(){
       $categories = Mage::getModel('catalog/category')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addIsActiveFilter();
		   $all = array();
       $i=0;
	     foreach ($categories as $c) {
                 
                $path = explode('/', $c->getPath());
                $string = '';
                foreach ($path as $pathId)
                {
                    $cat_name=Mage::getModel('catalog/category')->load($pathId);
                    $string.= $cat_name->getName(). ' &gt; ';
                    $cnt++;
                }
                $string.= ';' . $c->getId();
        
        
	        $all[$i] = array($c->getName(),$c->getId(),$string);
          $i++;
	     }
            return $all;
    }
    public function addCategory($name,$parent){

       // Create category object
      $category = Mage::getModel('catalog/category');
      $category->setStoreId(0); // No store is assigned to this category. 

      if(!isset($parent) || $parent===null || $parent===''){
        $id= 2;
        $cat = Mage::getModel('catalog/category')->load($id);
        $path = $cat->getPath();
        $rootCategory['path'] = $path;
        }else{
         $cat = Mage::getModel('catalog/category')->load($parent);
         $path = $cat->getPath();
         $rootCategory['path']=$path;
      }

      $rootCategory['name'] = $name;
      $rootCategory['description'] = "Category Description";
      $rootCategory['meta_title'] =  $name;
      $rootCategory['meta_keywords'] =  $name;
      $rootCategory['meta_description'] = "Meta Description";
      $rootCategory['display_mode'] = "PRODUCTS";
      $rootCategory['is_active'] = 1;
      $rootCategory['is_anchor'] = 1;

      $category->addData($rootCategory);

      try {
        $category->save();
        $rootCategoryId = $category->getId();
      }
      catch (Exception $e){
        echo $e->getMessage();
      }

      return $rootCategoryId;

    }
    public function getCategoryByName($name){

        $cat = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name', ''.$name.'');
        //$cat->delete();
        if($cat->getFirstItem()->getEntityId()){
            return $cat->getFirstItem()->getEntityId();
        }
        else{

            return FALSE;
        }
        //return false;
    }

    public function getsingle($item_id,$category){
        
        $config = Mage::getStoreConfig("ebay/settings");

        if(empty($config['global_id']) || empty($config['app_name'])) die('eBay Account not Configured');
        
        
      $passurl="http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=XML&appid=".$config['app_name']."&siteid=0&version=515&ItemID=".$item_id."&IncludeSelector=Variations,Description,Details,ItemSpecifics,ShippingCosts";
      
      $responseXml= file_get_contents($passurl);
      $responseDoc = new DomDocument();
      $responseDoc->loadXML($responseXml);
      $title = $responseDoc->getElementsByTagName('Title')->item(0)->nodeValue;
    
      $des = $responseDoc->getElementsByTagName('Description')->item(0)->nodeValue;
      $sku = $responseDoc->getElementsByTagName('ItemID')->item(0)->nodeValue;
    
      $qty = $responseDoc->getElementsByTagName('Quantity')->item(0)->nodeValue;
      $item_url = $responseDoc->getElementsByTagName('ViewItemURLForNaturalSearch')->item(0)->nodeValue;
      $s_cost = $responseDoc->getElementsByTagName('ShippingServiceCost')->item(0)->nodeValue;
      $price= $responseDoc->getElementsByTagName('ConvertedCurrentPrice')->item(0)->nodeValue;
      $total=$price;
      $itemSpecific = $responseDoc->getElementsByTagName('ItemSpecifics')->item(0);
      $listElements = $itemSpecific->getElementsByTagName('NameValueList');
      $i=0;
      foreach($listElements as $listElement){
        $name=$listElement->getElementsByTagName('Name')->item(0)->nodeValue;
        $value=$listElement->getElementsByTagName('Value')->item(0)->nodeValue;
         $model=Mage::getModel('eav/entity_setup','core_setup');
        $m = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode('catalog_product',$name);

        if(null===$m->getId()) { $this->createAttribute($name, $name, "text", "simple"); }
        $name_value_list[$i]=array($name,$value);
        $i++;
      }
     // $desc=mysql_real_escape_string($item_arr['Description']);
      $name_value_list=$name_value_list;
      
      
      
       /*---------------------------import variation------------------- */
      $option=$option_label=array();
      if($responseDoc->getElementsByTagName('Variation')){
          $variation_list = $responseDoc->getElementsByTagName('Variation');
      }
      $i=0;
      foreach($variation_list as $variation){
      $listElements = $variation->getElementsByTagName('NameValueList');
      $j=0;
      foreach($listElements as $listElement){
         $name=$listElement->getElementsByTagName('Name')->item(0)->nodeValue;
         $value=$listElement->getElementsByTagName('Value')->item(0)->nodeValue;
         $option[$j][$i]=$value; 
         $option_label[$j]=$name;
         $j++;
      }
      
      $i++;
      }
      $data_arr1['option']=$option;
      $data_arr1['option_label']=$option_label;
      
      /*--------------------------------end------------------------- */
      
      
      $picture_url= $responseDoc->getElementsByTagName('PictureURL');
      $z=0;
      foreach ($picture_url as $p){
          $p_url[$z]=$p->nodeValue;
          $z++;
      }
     
      if(empty($p_url)){
          $p_url[0]='http://thumbs.ebaystatic.com/pict/3193577262_3.jpg';
      }
      
      $days = $responseDoc->getElementsByTagName('HandlingTime')->item(0)->nodeValue;
       if(!empty($p_url)){
                    $importDir = Mage::getBaseDir('media') . DS . 'import' . DS;
                    $i=0;
                    foreach($p_url as $p){
                    $content=@file_get_contents($p);
                    $save = $item_id .$i. '.jpg'; 
                    $path[$i]=$importDir.''.$save;
                    @file_put_contents($path[$i],$content);
                    $i++;
                    }
                    if(!empty($content)){
                       if($this->check_item($sku)) {
                           
                          $product_id = $this->check_item($sku);
                          $this->update_item($product_id,$path,$total,$title,$des,$name_value_list,$days,$item_url,$total,$category,$option,$option_label,$qty,$item_id);
                           Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ebay/item')->__('eBay product with id:'.$item_id.' updated'));
                    
                        }
                        else{
                             $this->additem($item_id,$total,$title,$des,$sku,$path,$qty,$category,$name_value_list,$days,$item_url,$total,$option,$option_label);
                              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ebay/item')->__('eBay product with id '.$item_id.' imported successfully'));
                    
                        }
                    }
       }      
    }

    private function getDescription($item_id){

      $passurl="http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=XML&appid=sahycart-06ea-4a54-b6bf-bf7c8f18b0bd&siteid=0&version=515&ItemID=".$item_id."&IncludeSelector=Variations,Description,Details,ItemSpecifics";
      $responseXml= file_get_contents($passurl);
      $responseDoc = new DomDocument();
      $responseDoc->loadXML($responseXml);
      $data_arr1['description'] = $responseDoc->getElementsByTagName('Description')->item(0)->nodeValue;
      $data_arr1['sku'] = $responseDoc->getElementsByTagName('SKU')->item(0)->nodeValue;
      
      
      $itemSpecific = $responseDoc->getElementsByTagName('ItemSpecifics')->item(0);
      $listElements = $itemSpecific->getElementsByTagName('NameValueList');
      $a=0;
      foreach($listElements as $listElement){
         $name=$listElement->getElementsByTagName('Name')->item(0)->nodeValue;
         $value=$listElement->getElementsByTagName('Value')->item(0)->nodeValue;
         $m = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product',$name);

        if(null===$m->getId()) {
                $this->createAttribute($name, $name, "text", "simple");
        }
        $name_value_list[$a]=array($name,$value);
        $a++;
      }
     // $desc=mysql_real_escape_string($item_arr['Description']);
      $data_arr1['name_value_list']=$name_value_list;
      $data_arr1['picture_url'] = $responseDoc->getElementsByTagName('PictureURL');
      $data_arr1['days'] = $responseDoc->getElementsByTagName('HandlingTime')->item(0)->nodeValue;
      
          /*---------------------------import variation------------------- */
      $option=$option_label=array();
      if($responseDoc->getElementsByTagName('Variation')){
          $variation_list = $responseDoc->getElementsByTagName('Variation');
      }
      
      $i=0;
      foreach($variation_list as $variation){
      $listElements = $variation->getElementsByTagName('NameValueList');
      $j=0;
      foreach($listElements as $listElement){
         $name=$listElement->getElementsByTagName('Name')->item(0)->nodeValue;
         $value=$listElement->getElementsByTagName('Value')->item(0)->nodeValue;
         $option[$j][$i]=$value; 
         $option_label[$j]=$name;
         $j++;
      }
      
      $i++;
      }
      
      $data_arr1['option']=$option;
      $data_arr1['option_label']=$option_label;
      /*--------------------------------end------------------------- */
      
      return $data_arr1;
    }

    private function createAttribute($code, $label, $attribute_type, $product_type){
    $_attribute_data = array(
        'attribute_code' => $code,
        'is_global' => '1',
        'frontend_input' => $attribute_type, //'boolean',
        'default_value_text' => '',
        'default_value_yesno' => '0',
        'default_value_date' => '',
        'default_value_textarea' => '',
        'is_unique' => '0',
        'is_required' => '0',
        'apply_to' => array($product_type), //array('grouped')
        'is_configurable' => '0',
        'is_searchable' => '0',
        'is_visible_in_advanced_search' => '0',
        'is_comparable' => '0',
        'is_used_for_price_rules' => '0',
        'is_wysiwyg_enabled' => '0',
        'is_html_allowed_on_front' => '1',
        'is_visible_on_front' => '1',
        'used_in_product_listing' => '1',
        'used_for_sort_by' => '1',
        'frontend_label' => $label
    );
    $model = Mage::getModel('catalog/resource_eav_attribute');
    if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
        $_attribute_data['backend_type'] = $model->getBackendTypeByInput($_attribute_data['frontend_input']);
    }
    $defaultValueField = $model->getDefaultValueByInput($_attribute_data['frontend_input']);
    $model->addData($_attribute_data);
    
        $setId =Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
     
        $group = Mage::getModel('eav/entity_attribute_group')->getCollection()->addFieldToFilter('attribute_set_id',$setId)->setOrder('attribute_group_id',ASC)->getFirstItem();
        $groupId = $group->getId();
    
    $model->setAttributeSetId($setId);
    
    $model->setAttributeGroupId($groupId);
    
    
    
    $model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
    $model->setIsUserDefined(1);
    try {
        $model->save();
    } catch (Exception $e) { 
        //echo $code.'<p>Sorry, error occured while trying to save the attribute. Error: '.$e->getMessage().'</p>'; 
    }
}

    public function objectsIntoArray($arrObjData, $arrSkipIndices = array()) {

        $arrData = array();

            // if input is object, convert into array
            if (is_object($arrObjData)) {
                $arrObjData = get_object_vars($arrObjData);
            }

            if (is_array($arrObjData)) {
                foreach ($arrObjData as $index => $value) {
                    if (is_object($value) || is_array($value)) {
                        $value = $this->objectsIntoArray($value, $arrSkipIndices); // recursive call
                    }
                    if (in_array($index, $arrSkipIndices)) {
                        continue;
                    }
                    $arrData[$index] = $value;
                }
            }
            return $arrData;
            
        }
        
    public  function EbayHeaders($verb){

            $config = Mage::getStoreConfig("ebay/settings");

            if(empty($config['global_id']) || empty($config['app_name'])) die('eBay Account not Configured');
            
            $headers = array(
                    'X-EBAY-SOA-SERVICE-NAME:FindingService',
                    'X-EBAY-SOA-OPERATION-NAME:findItemsAdvanced',
                    'X-EBAY-SOA-SERVICE-VERSION:1.12.0',
                    'X-EBAY-SOA-GLOBAL-ID:'.$config['global_id'],
                    'X-EBAY-SOA-SECURITY-APPNAME:'.$config['app_name'],//sahycart-06ea-4a54-b6bf-bf7c8f18b0bd',
                    'X-EBAY-SOA-REQUEST-DATA-FORMAT:XML'
            ); 

            return $headers;
        }
        
    function sendHttpRequest($requestXmlBody,$headers){  

                $con = curl_init();
                curl_setopt($con, CURLOPT_POSTFIELDS, $requestXmlBody);
                curl_setopt($con, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($con, CURLOPT_POST, 1);
                curl_setopt($con, CURLOPT_URL, 'http://svcs.ebay.com/services/search/FindingService/v1');
                curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($con, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($con, CURLOPT_SSL_VERIFYPEER, 0);

                 $response = curl_exec($con);             
                 return $response;

    }
    public function import_items($item,$category,$check_by_id = TRUE) {
            set_time_limit(0);
            $size = sizeof($item);
            $items = 0;
            $updated=0;
            $error=array();
            $returns=array();
            for ($i = 0; $i < $size; $i++) {
                $item_id= $item[$i]['itemId'];

                $item_url= $item[$i]['viewItemURL'];        
                $itemid = trim($item[$i]['itemId']);
                $title = trim($item[$i]['title']);
                $model = time() . $itemid;

                $price=trim($item[$i]['sellingStatus']['currentPrice']);
                if($price<=0){
                    $price=$item[$i]['SellingStatus']['CurrentPrice'];
                }
                $ship_price=trim($item[$i]['shippingInfo']['shippingServiceCost']);
                
                   $category2_id=0;
                   $category_id=0;
                    $catname = trim($item[$i]['primaryCategory']['categoryName']);
                    $category_id = 0;
                   $parent=$category;
                    $catnames = preg_split('/,/', $catname);
                    if (is_array($catnames)) {
                        foreach ($catnames as $catname) {
                             if ($this->getCategoryByName($catname)) {
                                $category_id = $this->getCategoryByName($catname);
                             } else {
                               $category_id = $this->addCategory($catname, $parent);
                             }
                            $parent = $category_id;
                        }
                    } else {
                        if ($this->getCategoryByName($catname)) {
                            $category_id = $this->getCategoryByName($catname);
                        } else {
                            $category_id = $this->addCategory($catname, $parent);
                        }
                    }  
                    
                $total=$price;
                $des=null;//$this->getDescription($itemid);
                $country = trim($item[$i]['country']);
                $sellstatus = trim($item[$i]['sellingStatus']['sellingState']);
                $qty = '1';
                $url = trim($item[$i]['galleryURL']);
                $strttime = trim($item[$i]['listingInfo']['startTime']);
                $imgurl = null;//isset($item[$i]['pictureURLSuperSize'])?trim($item[$i]['pictureURLSuperSize']):trim($des['picture_url']); 
                $save = '';
                  
//                 $j=0;
//                 foreach ($des['picture_url'] as $p){
//                      $p_url[$j]=$p->nodeValue;
//                      $j++;
//                 }

                if(true){ //!empty($p_url)
//                    $importDir = Mage::getBaseDir('media') . DS . 'import' . DS;
//                        $k=0;
//                        foreach($p_url as $p){
//                        $content=@file_get_contents($p);
//                        $save = $item_id .$k. '.jpg'; 
//                        $path[$k]=$importDir.''.$save;
//                        @file_put_contents($path[$k],$content);
//                        $k++;
//                        }
                    
                   
                        if($this->check_item($des['sku'])) {
                          $product_id = $this->check_item($des['sku']);
                         // $this->update_item($product_id,$path,$total,$title,$des['description'],$des['name_value_list'],$des['days'],$item_url,$total,$category_id,$des['option'],$des['option_label'],$qty,$itemid);
                          //$this->update_item($product_id,$path,$total,$title,$des['description'],$des['name_value_list'],$des['days'],$item_url,$total,$category_id,$des['option'],$des['option_label'],$qty,$itemid);
                          $updated++;
                          continue;
                        }
                        else{
                            $this->additem($itemid,$total,$title,null,$des['sku'],null,$qty,$category_id,null,null,$item_url,$total,null,null); 
                            //$this->additem($itemid,$total,$title,$des['description'],$des['sku'],$path,$qty,$category_id,$des['name_value_list'],$des['days'],$item_url,$total,$des['option'],$des['option_label']); 
                        }
                    
                }

                $items++; 

            }
            $returns['total']=$items;
            $returns['updated']=$updated;
            $returns['error']=$error;
            return $returns;
        }

    public function check_item($sku){
         $product  = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);  ;
         if($product){
             return $product->getId();
         }
         else{
             return FALSE;
         }
    }
    public function update_item($itemId,$path,$price,$name,$desc,$name_value,$days,$item_url,$total,$category,$options,$option_label,$qty,$ebay_no){
        return;
        $pro    = Mage::getModel('catalog/product');
        $pro->load($itemId);
        $p=0;
        foreach($options as $op){
        $option_list[$p] = array(
        'title' => $option_label[$p],
        'type' => 'drop_down', // could be drop_down ,checkbox , multiple
        'is_require' => 1,
        'sort_order' => 0,
        'values' => $this->getOptions($op)
        );
        $p++;
        }
        $pro->setStockData(array(
         'is_in_stock' => 1,
         'qty' => ''.$qty.''
        ));
        $pro->setCategoryIds(array($category));
        $pro->setProductOptions($option_list);
        $pro->setCanSaveCustomOptions(true);
        $pro->save();
    }
    public function getOptions($option){
    $option=array_unique($option);    
    $q=0;
    foreach ($option as $op){
        $opt_value[$q]=array(
        'title' => $op,
        'price' =>'',
        'price_type' => 'fixed',
        'sku' => $op.$q,
        'sort_order' => '1'
        );
    $q++;
    }
    return $opt_value;
    }
}