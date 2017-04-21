<?php
/*------------------------------------------------------------------------
 # SM Zanafashion - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Zanafashion_Model_System_Config_Source_ListBodyFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'Arial', 'label'=>Mage::helper('zanafashion')->__('Arial')),
			array('value'=>'Arial Black', 'label'=>Mage::helper('zanafashion')->__('Arial-black')),
			array('value'=>'Courier New', 'label'=>Mage::helper('zanafashion')->__('Courier New')),
			array('value'=>'Georgia', 'label'=>Mage::helper('zanafashion')->__('Georgia')),
			array('value'=>'Impact', 'label'=>Mage::helper('zanafashion')->__('Impact')),
			array('value'=>'Lucida Console', 'label'=>Mage::helper('zanafashion')->__('Lucida-console')),
			array('value'=>'Lucida Grande', 'label'=>Mage::helper('zanafashion')->__('Lucida-grande')),
			array('value'=>'Palatino', 'label'=>Mage::helper('zanafashion')->__('Palatino')),
			array('value'=>'Tahoma', 'label'=>Mage::helper('zanafashion')->__('Tahoma')),
			array('value'=>'Times New Roman', 'label'=>Mage::helper('zanafashion')->__('Times New Roman')),	
			array('value'=>'Trebuchet', 'label'=>Mage::helper('zanafashion')->__('Trebuchet')),	
			array('value'=>'Verdana', 'label'=>Mage::helper('zanafashion')->__('Verdana'))		
		);
	}
}
