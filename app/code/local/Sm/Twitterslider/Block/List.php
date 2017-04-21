<?php
/*------------------------------------------------------------------------
 # SM Twitter Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Twitterslider_Block_List extends Mage_Catalog_Block_Product_Abstract
{
	protected $_config = null;
	protected $_storeId = null;
	protected $_productCollection = null;
	
	public function __construct($attributes = array()){
		parent::__construct();
		$this->_config = Mage::helper('twitterslider/data')->get($attributes);
	}
	
	public function getConfig($name=null, $value=null){
		if (is_null($this->_config)){
			$this->_config = Mage::helper('twitterslider/data')->get(null);
		}
		if (!is_null($name) && !empty($name)){
			$valueRet = isset($this->_config[$name]) ? $this->_config[$name] : $value;
			return $valueRet;
		}
		return $this->_config;
	}
	
	public function setConfig($name, $value=null){
		if (is_null($this->_config)) $this->getConfig();
		if (is_array($name)){
			$this->_config = array_merge($this->_config, $name);
			return;
		}
		if (!empty($name)){
			$this->_config[$name] = $value;
		}
		return true;
	}
	
	protected function _toHtml(){
		if(!$this->_config['isenabled']) return;
		$template = 'default';
		$template_file = "sm/twitterslider/".$template.".phtml";
		$this->setTemplate($template_file);
		return parent::_toHtml();
	}
	
	public function _getList(){
		if(!$this->_config['isenabled']) return;
		$list = array();
		if(!class_exists('TwitterOAuth')){
			require_once 'twitteroauth.php';
		}
	
		$consumerKey = $this->getConfig('consumekey');
		$consumerSecret = $this->getConfig('consumersecret');
		$oAuthToken = null;
		$oAuthSecret = null;
		$screenName = $this->getConfig('screenname');
		if(!$consumerKey || !$consumerSecret || !$screenName) return;
		$notweets = $this->getConfig('count','6');
		$Tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);
		$tweets = $Tweet->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$screenName."&count=".$notweets);
		//$search ='#checkout';
		//$search = str_replace("#", "%23", $search);
		//$tweets = $Tweet->get("https://api.twitter.com/1.1/search/tweets.json?q=".$search."&count=".$notweets);
		$items = json_decode($tweets);
		if(empty($items) || isset($items->errors)) return;
		foreach($items as $item){
			$text = preg_replace( '@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@','<a target="_blank" href="$1">$1</a>',$item->text);
			$text = preg_replace( '/@(\w+)/','<a target="_blank" href="http://twitter.com/$1">@$1</a>',$text);
			$text = preg_replace('/(?:^|\s)+#(\w+)/',' <a target="_blank" href="http://search.twitter.com/search?q=%23$1">#$1</a>',$text);
			$item->_text = $text;
			$item->_full_name = $item->user->name;
			$item->_username = '@'.$item->user->screen_name;
			$item->_twitter_link = 'http://www.twitter.com/'.$item->user->screen_name;
			$item->_image = $item->user->profile_image_url;
			$list[] = $item;
		}
		return $list;
	}
	
	public function getScriptTags(){
		$import_str = "";
		$jsHelper = Mage::helper('core/js');
		if (null == Mage::registry('jsmart.jquery')){
			// jquery has not added yet
			if (Mage::getStoreConfigFlag('twitterslider_cfg/advanced/include_jquery')){
				// if module allowed jquery.
				$import_str .= $jsHelper->includeSkinScript('sm/twitterslider/js/jquery-1.8.2.min.js');
				$import_str .= $jsHelper->includeSkinScript('sm/twitterslider/js/jquery-noconflict.js');
				Mage::register('jsmart.jquery', 1);
			}
		}
		
		if (null == Mage::registry('jsmart.twitterslider.js')){
			// add script for this module.
			$import_str .= $jsHelper->includeSkinScript('sm/twitterslider/js/jcarousel.js');
			$import_str .= $jsHelper->includeSkinScript('sm/twitterslider/js/jquery.cj-swipe.js');
			Mage::register('jsmart.twitterslider.js', 1);
		}
		return $import_str;
	}
	
	
}
