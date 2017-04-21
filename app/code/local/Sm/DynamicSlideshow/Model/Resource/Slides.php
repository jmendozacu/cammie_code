<?php
/**
 * @package SM Dynamic Slideshow 
 * @version 2.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright Copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.magentech.com
 */

class Sm_DynamicSlideshow_Model_Resource_Slides extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init( 'dynamicslideshow/slides', 'id' );
    }
}