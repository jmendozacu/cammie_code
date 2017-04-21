<?php
/*------------------------------------------------------------------------
 # SM Twitter Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Twitterslider_Helper_Data extends Mage_Core_Helper_Abstract {
	public function __construct(){
		$this->defaults = array(
			/* General setting */
			'isenabled'					=> '1',
			'title' 					=> 'SM [Modulename]',
			'screenname' 				=> '',				
			'consumekey'				=> '',					
			'consumersecret' 			=> '',
			'count' 					=> 6,  				
			'display_avatars'			=> 1,
			'display_follow_button'		=> 1,
			'display_direction_button'	=> 1,
			'start'						=> 1,
			'play' 						=> 1,
			'pause_hover' 				=> 1,
			'effect' 					=> 'slide',
			'swipe_enable'				=> '',			
			'include_jquery'			=> 1,
			'pretext'					=> '',
			'posttext'					=> ''
			
			/**config_fields**/
		);
	}

	function get($attributes=array())
	{
		$data = $this->defaults;
		$general 					= Mage::getStoreConfig("twitterslider_cfg/general");
		$advanced 					= Mage::getStoreConfig("twitterslider_cfg/advanced");
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		if (is_array($general))					$data = array_merge($data, $general);
		if (is_array($advanced)) 				$data = array_merge($data, $advanced);
		
		return array_merge($data, $attributes);;
	}
}
?>