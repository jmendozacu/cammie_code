<?php
/*------------------------------------------------------------------------
 # SM Megashop - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Zanafashion_Model_System_Config_Source_ListLayoutHeader
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'1', 'label'=>Mage::helper('zanafashion')->__('Header default')),			
			array('value'=>'2', 'label'=>Mage::helper('zanafashion')->__('Header 02')),
			array('value'=>'3', 'label'=>Mage::helper('zanafashion')->__('Header 03')),
			array('value'=>'4', 'label'=>Mage::helper('zanafashion')->__('Header 04')),
			array('value'=>'5', 'label'=>Mage::helper('zanafashion')->__('Header 05'))
		);
	}
}
