<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Frank N�gler <typo3@naegler.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/ 
/**
 * $Id$
 */

require_once(PATH_t3lib.'class.t3lib_tceforms.php');
require_once(PATH_feuserregister . 'classes/transformer/class.tx_feuserregister_abstracttransformer.php');

class tx_feuserregister_model_Field {
	const PARSE_DATEBASE	= 'db';
	const PARSE_HTML		= 'html';
	const TYPE_PASSWORD		= 'password';
	const TYPE_TCA			= 'TCA';
	const TYPE_TEXT			= 'text';
	const TYPE_TEXTAREA		= 'textarea';
	
	protected $_databaseTransformers = array();
	protected $_errorString = '';
	protected $_fieldName = null;
	protected $_fieldConfiguration = null;
	protected $_htmlField = null;
	protected $_htmlTransformers = array();
	protected $_isValid = true;
	protected $_isRequired = false;
	protected $_request = null;
	protected $_tcaField = null;
	protected $_validators = array();
	protected $_value = '';
	/**
	 * @var tx_feuserregister_LocalizationManager
	 */
	protected $_localizationManager = null;
	
	public function __construct($fieldname) {
		$this->_fieldName = $fieldname;
		$this->_fieldConfiguration = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']['fields.'][$fieldname.'.'];
		$this->_localizationManager = tx_feuserregister_LocalizationManager::getInstance(
			'EXT:feuserregister/lang/locallang_fields.xml', 
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']
		);
		$this->_request = t3lib_div::GParrayMerged('tx_feuserregister');
		$sessionUser = t3lib_div::makeInstance('tx_feuserregister_model_SessionUser');
		$this->_value = (isset($this->_request['data'][$this->_fieldName])) ? $this->_request['data'][$this->_fieldName] : $sessionUser->get($this->_fieldName);
		
			// init validators
		$validators = $this->_fieldConfiguration['validators'];
		if (strlen($validators)) {
			$validators = t3lib_div::trimExplode(',', $validators);
		}
		if (is_array($validators)) {
			foreach ($validators as $validator) {
				$validatorObject = tx_feuserregister_ValidatorFactory::getValidator($validator);
				$validatorObject->setFieldname($this->_fieldName);
				if (is_array($this->_fieldConfiguration['validatorOptions.'][$validatorObject->getName().'.'])) {
					$validatorObject->setOptions($this->_fieldConfiguration['validatorOptions.'][$validatorObject->getName().'.']);
				}
				$this->_validators[] = $validatorObject;
			}
			if (in_array('required', $validators)) {
				$this->_isRequired = true;
			}
		}

			// init transformers
		$transformers = $this->_fieldConfiguration['transformers'];
		if (strlen($transformers)) {
			$transformers = t3lib_div::trimExplode(',', $transformers);
		}
		if (is_array($transformers)) {
			foreach ($transformers as $transformer) {
				$transformerObject = tx_feuserregister_TransformerFactory::getTransformer($transformer);
				if (is_array($this->_fieldConfiguration['transformerOptions.'][$transformerObject->getName().'.'])) {
					$transformerObject->setOptions($this->_fieldConfiguration['transformerOptions.'][$transformerObject->getName().'.']);
				}
				switch ($transformerObject->getType()) {
					case tx_feuserregister_AbstractTransformer::TYPE_DATABASE:
						$this->_databaseTransformers[] = $transformerObject;
					break;
					case tx_feuserregister_AbstractTransformer::TYPE_HTML:
						$this->_htmlTransformers[] = $transformerObject;
					break;
				}
			}
		}

		switch ($this->_fieldConfiguration['type']) {
			case self::TYPE_PASSWORD:
				$this->_createPasswordField();
			break;
			case self::TYPE_TCA:
				$this->_createTCAField();
			break;
			case self::TYPE_TEXT:
				$this->_createTextField();
			break;
			case self::TYPE_TEXTAREA:
				$this->_createTextareaField();
			break;
			default:
				$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_Field');
				throw new $exceptionClass('unknown field type: ' . $this->_fieldConfiguration['type'] . ' for field ' . $this->_fieldName);
			break;
		}
	}
	
	public function getErrorString() {
		return $this->_errorString;
	}
	
	public function getField() {
		return $this->_htmlField;
	}
	
	public function getFieldName() {
		return $this->_fieldName;
	}
	
	public function getLabel() {
		if ($this->_fieldConfiguration['type'] == 'TCA') {
			return $this->_tcaField->getLabel();
		}
		return $this->_localizationManager->getLL('label_field_'.$this->_fieldName);
	}
	
	public function getRequiredString() {
		return ($this->_isRequired) ? $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']['global.']['requiredString'] : '';
	}
	
	public function getValue($format = null) {
		switch ($format) {
			case self::PARSE_DATEBASE:
				$value = $this->_prepareForDatabase();
				if (count($this->_databaseTransformers)) {
					foreach ($this->_databaseTransformers as $transformer) {
						$transformer->setValue($value);
						$value = $transformer->transform();
					}
				}
			break;
			case self::PARSE_HTML:
				$value = $this->_prepareForHtml();
				if (count($this->_htmlTransformers)) {
					foreach ($this->_htmlTransformers as $transformer) {
						$transformer->setValue($value);
						$value = $transformer->transform();
					}
				}
			break;
			default:
				return $this->_value;
			break;
		}
		return $value;
	}
	
	public function setErrorString($errorString) {
		return $this->_errorString = $errorString;
	}
	
	public function validate() {
		$this->_isValid = true;
		if (count($this->_validators)) {
			foreach ($this->_validators as $validator) {
				$validator->setValue($this->_value);
				$isValid = $validator->validate();
				if (!$isValid) {
					$this->_errorString = $validator->getErrorMessage();
					$this->_isValid = false;
					break;
				}
			}
		}
		return $this->_isValid;
	}
	
	protected function _createTextField() {
		$value = $this->getValue(self::PARSE_HTML);
		$attributes = array();
		$attributes[] = "type=\"text\"";
		$attributes[] = "name=\"tx_feuserregister[data][{$this->_fieldName}]\"";
		$attributes[] = "value=\"{$value}\"";
		if ($this->_fieldConfiguration['maxLength']) {
			$attributes[] = 'maxlength="'.$this->_fieldConfiguration['maxLength'].'"';
		}
		if ($this->_fieldConfiguration['additionalAttributes']) {
			$attributes[] = $this->_fieldConfiguration['additionalAttributes'];
		}
		
		$this->_htmlField = '<input '.implode(' ', $attributes).'/>';
	}
	
	protected function _createPasswordField() {
		$value = $this->getValue(self::PARSE_HTML);
		$attributes = array();
		$attributes[] = "type=\"password\"";
		$attributes[] = "name=\"tx_feuserregister[data][{$this->_fieldName}]\"";
		$attributes[] = "value=\"{$value}\"";
		if ($this->_fieldConfiguration['maxLength']) {
			$attributes[] = 'maxlength="'.$this->_fieldConfiguration['maxLength'].'"';
		}
		if ($this->_fieldConfiguration['additionalAttributes']) {
			$attributes[] = $this->_fieldConfiguration['additionalAttributes'];
		}
		
		$this->_htmlField = '<input '.implode(' ', $attributes).'/>';
	}
	
	protected function _createTextareaField() {
		$value = $this->getValue(self::PARSE_HTML);
		$attributes = array();
		$attributes[] = "name=\"tx_feuserregister[data][{$this->_fieldName}]\"";
		if ($this->_fieldConfiguration['maxLength']) {
			$attributes[] = 'maxlength="'.$this->_fieldConfiguration['maxLength'].'"';
		}
		if ($this->_fieldConfiguration['additionalAttributes']) {
			$attributes[] = $this->_fieldConfiguration['additionalAttributes'];
		}
		
		$this->_htmlField = '<textarea '.implode(' ', $attributes).'>'.$value.'</textarea>';
	}
	
	protected function _createTCAField() {
		$GLOBALS['TSFE']->includeTCA();
		$fieldConfig = $GLOBALS['TCA']['fe_users']['columns'][$this->_fieldName];
		$this->_tcaField = tx_feuserregister_TcaFieldFactory::getTcaField($fieldConfig['config']['type']);
		$this->_tcaField->setConfiguration($fieldConfig);
		$this->_tcaField->setFieldConfiguration($this->_fieldConfiguration);
		$this->_tcaField->setValue($this->_value);
		$this->_tcaField->setFieldname($this->_fieldName);
		$this->_tcaField->setTransformers($this->_databaseTransformers, tx_feuserregister_AbstractTransformer::TYPE_DATABASE);
		$this->_tcaField->setTransformers($this->_htmlTransformers, tx_feuserregister_AbstractTransformer::TYPE_HTML);
		$this->_htmlField = $this->_tcaField->getHtmlField();
	}
	
	protected function _prepareForDatabase() {
		$value = $this->_value;
		switch ($this->_fieldConfiguration['type']) {
			case self::TYPE_PASSWORD:
					// we don't need to prepare this field type
					// use transformer if you need support for md5 or salted passwords
			break;
			case self::TYPE_TCA:
				$value = $this->_tcaField->getValue(tx_feuserregister_AbstractTcaField::PARSE_DATABASE);
			break;
			case self::TYPE_TEXT:
					// at this moment we don't need to prepare this field type
			break;
			case self::TYPE_TEXTAREA:
					// at this moment we don't need to prepare this field type
			break;
			default:
				$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_Field');
				throw new $exceptionClass('unknown field type: ' . $this->_fieldConfiguration['type'] . ' for field ' . $this->_fieldName);
			break;
		}
		return $value;
	}
		
	protected function _prepareForHtml() {
		$value = $this->_value;
		switch ($this->_fieldConfiguration['type']) {
			case self::TYPE_PASSWORD:
				if ($this->_fieldConfiguration['maskOnPreview']) {
					$value = str_repeat('*', strlen($value));
				}
				$value = htmlspecialchars($value);
			break;
			case self::TYPE_TCA:
				$value = $this->_tcaField->getValue(tx_feuserregister_AbstractTcaField::PARSE_HTML);
			break;
			case self::TYPE_TEXT:
				$value = htmlspecialchars($value);
			break;
			case self::TYPE_TEXTAREA:
				$value = nl2br(htmlspecialchars($value));
			break;
			default:
				$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_Field');
				throw new $exceptionClass('unknown field type: ' . $this->_fieldConfiguration['type'] . ' for field ' . $this->_fieldName);
			break;
		}
		return $value;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_field.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_field.php']);
}

?>