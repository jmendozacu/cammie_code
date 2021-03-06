<?php
/**
 * @package SM Dynamic Slideshow 
 * @version 2.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright Copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.magentech.com
 */

class Sm_DynamicSlideshow_Block_Adminhtml_Widget_Form_Url extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate( 'sm/dynamicslideshow/widget/form/url.phtml' );
    }
    
    public function getElement()
    {
        return $this->_element;
    }
    
    public function setElement( Varien_Data_Form_Element_Abstract $element )
    {
        return $this->_element = $element;
    }
    
    public function render( Varien_Data_Form_Element_Abstract $element )
    {
        $this->setElement( $element );
        return $this->toHtml();
    }
    
    public function getBtn()
    {
        return $this->getChildHtml( 'getBtn' );
    }
    
    protected function _prepareLayout()
    {
        $getBtn = $this->getLayout()->createBlock( 'adminhtml/widget_button' )->setData( array(
             'label' => "<i class='fa fa-download'></i>",
            'title' => Mage::helper( 'dynamicslideshow' )->__( 'Get Image' ),
            'onclick' => 'return DnmSl.updateContainer()' 
        ) );
        $this->setChild( 'getBtn', $getBtn );
        
        parent::_prepareLayout();
    }
}