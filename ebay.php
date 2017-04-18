<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ebay
 *
 * @author version
 */
class ebay {
    public function getsingle($item_id){
   /**
     * Get the resource model
     */
    $resource = Mage::getSingleton('core/resource');
     
    /**
     * Retrieve the read connection
     */
    $readConnection = $resource->getConnection('core_read');
    
      $table = $resource->getTableName('core/config_data');
     
    /**
     * Set the product ID
     */
     
    $query = "SELECT value FROM " . $table . " WHERE path='ebay/settings/app_name'"; 
     
    
    /**
     * Execute the query and store the results in $results
     */
    $results = $readConnection->fetchAll($query);
     
    /**
     * Print out the results
     */
     ;


      $passurl="http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=XML&appid=".$results[0]['value']."&siteid=77&version=515&ItemID=".$item_id."&IncludeSelector=Variations,Description,Details,ItemSpecifics,ShippingCosts,QuantitySold,Quantity";
      
      $responseXml= file_get_contents($passurl);
      
      $responseDoc = new DomDocument();
      $responseDoc->loadXML($responseXml);
      
    
      $des = $responseDoc->getElementsByTagName('Description')->item(0)->nodeValue;
      $soldqty = $responseDoc->getElementsByTagName('QuantitySold')->item(0)->nodeValue;
      $qty = $responseDoc->getElementsByTagName('Quantity')->item(0)->nodeValue;
    
      $itemSpecific = $responseDoc->getElementsByTagName('ItemSpecifics')->item(0);
      $listElements = $itemSpecific->getElementsByTagName('NameValueList');
      $i=0;
      foreach($listElements as $listElement){
        $name=$listElement->getElementsByTagName('Name')->item(0)->nodeValue;
        $value=$listElement->getElementsByTagName('Value')->item(0)->nodeValue;
        $m = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode('catalog_product',$name);

        if(null===$m->getId()) { $this->createAttribute($name, $name, "text", "simple"); }
        $name_value_list[$i]=array($name,$value);
        $i++;
      }//for each attribute
       
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
      $data_arr1['option_label']= $option_label;
      
      /*--------------------------------end------------------------- */
      
      
      $picture_url= $responseDoc->getElementsByTagName('PictureURL');
      $z=0;
      foreach ($picture_url as $p){
          $p_url[$z]=$p->nodeValue;
          $z++;
          break;
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
                    }//each photos
                    
                    $result['description'] = $des;
                    $result['soldqty'] = $soldqty;
                    $result['qty'] = $qty;
                    $result['path'] = $path;
                    $result['name_value_list'] = $name_value_list;
                    $result['option'] = $option;
                    $result['option_label'] = $option_label;
                  
                    return $result;
                    
       }//if photos      
    }//end of getsingle
    
    
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
    }//end of create attribute
}

?>
