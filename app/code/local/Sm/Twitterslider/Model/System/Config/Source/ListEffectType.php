<?php
/*------------------------------------------------------------------------
 # SM Twitter Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Twitterslider_Model_System_Config_Source_ListEffectType
{
	public function toOptionArray()
	{
		return array(
		array('value'=>'slide', 'label'=>Mage::helper('twitterslider')->__('Slide')),
		array('value'=>'fade', 'label'=>Mage::helper('twitterslider')->__('Fade')),
		);
	}
}
