<?php
/*------------------------------------------------------------------------
 # SM Zanafashion - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Zanafashion_Model_System_Config_Source_ListGoogleFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'', 'label'=>Mage::helper('zanafashion')->__('No select')),
			array('value'=>'Roboto', 'label'=>Mage::helper('zanafashion')->__('Roboto')),
			array('value'=>'Anton', 'label'=>Mage::helper('zanafashion')->__('Anton')),
			array('value'=>'Questrial', 'label'=>Mage::helper('zanafashion')->__('Questrial')),
			array('value'=>'Kameron', 'label'=>Mage::helper('zanafashion')->__('Kameron')),
			array('value'=>'Oswald', 'label'=>Mage::helper('zanafashion')->__('Oswald')),
			array('value'=>'Open Sans', 'label'=>Mage::helper('zanafashion')->__('Open Sans')),
			array('value'=>'BenchNine', 'label'=>Mage::helper('zanafashion')->__('BenchNine')),
			array('value'=>'Droid Sans', 'label'=>Mage::helper('zanafashion')->__('Droid Sans')),
			array('value'=>'Droid Serif', 'label'=>Mage::helper('zanafashion')->__('Droid Serif')),
			array('value'=>'PT Sans', 'label'=>Mage::helper('zanafashion')->__('PT Sans')),
			array('value'=>'Vollkorn', 'label'=>Mage::helper('zanafashion')->__('Vollkorn')),
			array('value'=>'Ubuntu', 'label'=>Mage::helper('zanafashion')->__('Ubuntu')),
			array('value'=>'Neucha', 'label'=>Mage::helper('zanafashion')->__('Neucha')),
			array('value'=>'Cuprum', 'label'=>Mage::helper('zanafashion')->__('Cuprum'))	
		);
	}
}
