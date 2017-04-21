<?php
/*------------------------------------------------------------------------
 # SM Zanafashion - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Zanafashion_Model_System_Config_Source_ListColor
{
	public function toOptionArray()
	{	
		return array(
		array('value'=>'default', 'label'=>Mage::helper('zanafashion')->__('Default')),
		array('value'=>'blue', 'label'=>Mage::helper('zanafashion')->__('Blue')),
		array('value'=>'green', 'label'=>Mage::helper('zanafashion')->__('Green')),
		array('value'=>'red', 'label'=>Mage::helper('zanafashion')->__('Red')),
		array('value'=>'violet', 'label'=>Mage::helper('zanafashion')->__('Violet')),
		array('value'=>'pink', 'label'=>Mage::helper('zanafashion')->__('Pink'))	
		);
	}
}
