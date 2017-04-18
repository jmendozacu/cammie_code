<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Magento_Attribute_Builder
{
    const TYPE_TEXT            = 'text';
    const TYPE_TEXTAREA        = 'textarea';
    const TYPE_SELECT          = 'select';
    const TYPE_MULTIPLE_SELECT = 'multiselect';
    const TYPE_BOOLEAN         = 'boolean';
    const TYPE_PRICE           = 'price';
    const TYPE_DATE            = 'date';

    const SCOPE_STORE   = 0;
    const SCOPE_GLOBAL  = 1;
    const SCOPE_WEBSITE = 2;

    const CODE_MAX_LENGTH = 30;

    /** @var Mage_Eav_Model_Entity_Attribute */
    private $attributeObj = null;

    private $code;
    private $primaryLabel;
    private $inputType;

    private $entityTypeId;

    private $options = array();
    private $params = array();

    //########################################

    public function save()
    {
        $this->init();
        return $this->saveAttribute();
    }

    // ---------------------------------------

    private function init()
    {
        if (is_null($this->entityTypeId)) {
            $this->entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
        }

        if (is_null($this->inputType)) {
            $this->inputType = self::TYPE_TEXT;
        }

        $this->attributeObj = Mage::getModel('eav/entity_attribute')
                                        ->loadByCode($this->entityTypeId, $this->code);

        return $this;
    }

    private function saveAttribute()
    {
        if ($this->attributeObj->getId()) {
            return array('result' => true, 'obj' => $this->attributeObj);
        }

        if (!$this->validate()) {
            return array('result' => false, 'error' => 'Attribute builder. Validation failed.');
        }

        $this->attributeObj = Mage::getModel('catalog/resource_eav_attribute');

        $data = $this->params;
        $data['attribute_code'] = $this->code;
        $data['frontend_label'] = array(Mage_Core_Model_App::ADMIN_STORE_ID => $this->primaryLabel);
        $data['frontend_input'] = $this->inputType;
        $data['entity_type_id'] = $this->entityTypeId;
        $data['is_user_defined']   = 1;

        $data['source_model']  = Mage::helper('catalog/product')->getAttributeSourceModelByInputType($this->inputType);
        $data['backend_model'] = Mage::helper('catalog/product')->getAttributeBackendModelByInputType($this->inputType);
        $data['backend_type']  = $this->attributeObj->getBackendTypeByInput($this->inputType);

        !isset($data['is_global'])               && $data['is_global'] = self::SCOPE_STORE;
        !isset($data['is_configurable'])         && $data['is_configurable'] = 0;
        !isset($data['is_filterable'])           && $data['is_filterable'] = 0;
        !isset($data['is_filterable_in_search']) && $data['is_filterable_in_search'] = 0;
        !isset($data['apply_to'])                && $data['apply_to'] = array();

        $this->prepareOptions($data);
        $this->prepareDefault($data);

        $this->attributeObj->addData($data);

        try {
            $this->attributeObj->save();
        } catch (Exception $e) {
            return array('result' => false, 'error' => $e->getMessage());
        }

        return array('result' => true, 'obj' => $this->attributeObj);
    }

    private function validate()
    {
        $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{1,254}$/'));
        if (!$validatorAttrCode->isValid($this->code)) {
            return false;
        }

        if (strlen($this->code) > self::CODE_MAX_LENGTH) {
            return false;
        }

        if (empty($this->primaryLabel)) {
            return false;
        }

        /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
        $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
        if (!$validatorInputType->isValid($this->inputType)) {
            return false;
        }

        return true;
    }

    //########################################

    public static function generateCodeByLabel($primaryLabel)
    {
        $attributeCode = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $primaryLabel);
        $attributeCode = preg_replace('/[^0-9a-z]/i','_', $attributeCode);
        $attributeCode = preg_replace('/_+/', '_', $attributeCode);

        $abc = 'abcdefghijklmnopqrstuvwxyz';
        if (preg_match('/^\d{1}/', $attributeCode, $matches)) {
            $index = $matches[0];
            $attributeCode = $abc[$index].'_'.$attributeCode;
        }

        return strtolower($attributeCode);
    }

    //########################################

    private function prepareOptions(&$data)
    {
        $options = $this->options;

        if (!empty($this->params['default_value'])) {

            if ($this->isSelectType()) {
                $options[] = (string)$this->params['default_value'];
            }

            if ($this->isMultipleSelectType()) {

                is_array($this->params['default_value'])
                    ? $options = array_merge($options, $this->params['default_value'])
                    : $options[] = (string)$this->params['default_value'];
            }
        }

        foreach (array_unique($options) as $optionValue) {
            $code = $this->getOptionCode($optionValue);
            $data['option']['value'][$code] = array(Mage_Core_Model_App::ADMIN_STORE_ID => $optionValue);
        }
    }

    private function getOptionCode($optionValue)
    {
        return 'option_'.substr(sha1($optionValue), 0, 6);
    }

    //----------------------------------------

    private function prepareDefault(&$data)
    {
        if ($this->isDateType() || $this->isBooleanType() || $this->isTextAreaType() || $this->isTextType()) {

            $data['default_value'] = (string)$this->params['default_value'];
            return;
        }

        if ($this->isSelectType() || $this->isMultipleSelectType()) {

            $defaultValues = is_array($this->params['default_value']) ? $this->params['default_value']
                                                                      : array($this->params['default_value']);

            $data['default_value'] = null;
            foreach ($defaultValues as $value) {
                $data['default'][] = $this->getOptionCode($value);
            }

            return;
        }
    }

    //########################################

    public function setCode($value)
    {
        $this->code = $value;
        return $this;
    }

    public function setLabel($value)
    {
        $this->primaryLabel = $value;
        return $this;
    }

    public function setInputType($value)
    {
        $this->inputType = $value;
        return $this;
    }

    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    public function setParams(array $value = array())
    {
        $this->params = $value;
        return $this;
    }

    /**
     * Can be string|int or array for multi select attribute
     * @param $value
     * @return $this
     */
    public function setDefaultValue($value)
    {
        $this->params['default_value'] = $value;
        return $this;
    }

    public function setScope($value)
    {
        $this->params['is_global'] = $value;
        return $this;
    }

    public function setEntityTypeId($value)
    {
        $this->entityTypeId = $value;
        return $this;
    }

    //########################################

    public function isSelectType()
    {
        return $this->inputType == self::TYPE_SELECT;
    }

    public function isMultipleSelectType()
    {
        return $this->inputType == self::TYPE_MULTIPLE_SELECT;
    }

    public function isBooleanType()
    {
        return $this->inputType == self::TYPE_BOOLEAN;
    }

    public function isTextType()
    {
        return $this->inputType == self::TYPE_TEXT;
    }

    public function isTextAreaType()
    {
        return $this->inputType == self::TYPE_TEXTAREA;
    }

    public function isDateType()
    {
        return $this->inputType == self::TYPE_DATE;
    }

    //########################################
}