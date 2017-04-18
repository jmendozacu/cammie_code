<?php
    set_time_limit(0); 
    require 'app/Mage.php';
    require 'ebay.php';
    Mage::app('admin')->setUseSessionInUrl(false);

    $id = empty($_GET['id'])?$_POST['product_id']:$_GET['id'];

    $pro  = Mage::getModel('catalog/product')->load($id);

    $ebay = new ebay;
    if($pro->getSku())
    {
    
    $result = $ebay->getsingle($pro->getSku());
    
    foreach($result['name_value_list'] as $nv){
        $pro->setData($nv[0],$nv[1]);
    }

     /*--------------variation start-------------*/
    
    $i=0;
    foreach($result['option'] as $op){
        $option_list_[$i] = array(
            'title' => $result['option_label'][$i],
            'type' => 'drop_down', // could be drop_down ,checkbox , multiple
            'is_require' => 1,
            'sort_order' => 0,
            'values' => $ebay->getOptions($op)
        );
        $i++;
    }
    $pro->setCanSaveCustomOptions(true);
    $pro->setProductOptions($option_list_);
    
      /*--------------variation end-------------*/


    $count=0;
    foreach ($result['path'] as $p) :
        if ($count == 0) :
            $pro->addImageToMediaGallery($p, array('thumbnail','image','small_image'), false); 
        else :
             $pro->addImageToMediaGallery($p,null, false, false );
        endif;
            $count++;  
    endforeach; 
    $result['soldqty']."-";
    $result['qty']."-";
    $qty=$result['qty']-$result['soldqty'];
    $pro->setDescription($result['description']);
   
    $stockData = array();
    $stockData['qty'] = $qty;
    $stockData['is_in_stock'] = 1;
    $stockData['manage_stock'] = 1;
    $stockData['use_config_manage_stock'] = 0;
    $pro->setStockData($stockData);
    $pro->save();
    
    echo 'Imported -> '.$pro->getId();
    ?>
    <script>
   //if(confirm('continue?')){
        location = 'qty.php?id=<?php echo $id+1; ?>';
   // }
    </script>
    
    <?php } else { echo "Import All Products Details";} ?>
    