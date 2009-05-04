<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Frank Naegler <typo3@naegler.net>
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
	const TYPE_HIDDEN		= 'hidden';
	const TYPE_PASSWORD		= 'password';
	const TYPE_TCA			= 'TCA';
	const TYPE_TEXT			= 'text';
	const TYPE_TEXTAREA		= 'textarea';
	
	protected $_configuration = array();
	protected $_controller = null;
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
		$this->_configuration = tx_feuserregister_Registry::get('tx_feuserregister_configuration');
		$this->_fieldConfiguration = $this->_configuration['fields.'][$fieldname.'.'];
		
		$this->_controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');
		
		$this->_localizationManager = tx_feuserregister_LocalizationManager::getInstance(
			'EXT:feuserregister/lang/locallang_fields.xml', 
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']
		);
		$this->_request = t3lib_div::makeInstance('tx_feuserregister_Request');
		$requestData	= $this->_request->get('data');
		$sessionUser = t3lib_div::makeInstance('tx_feuserregister_model_SessionUser');
		if (strlen(trim($sessionUser->get($this->_fieldName))) == 0 && isset($this->_fieldConfiguration['aliasField'])) {
			$sessionUser->set($this->_fieldName, $sessionUser->get($this->_fieldConfiguration['aliasField']));
		}
		$this->_value = (isset($requestData[$this->_fieldName])) ? $requestData[$this->_fieldName] : $sessionUser->get($this->_fieldName);
		$this->_controller->notifyObservers('afterInitFieldValue', array('field' => &$this));
		
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
		$dbTransformers = $this->_fieldConfiguration['transformers.']['db'];
		if (strlen($dbTransformers)) {
			$dbTransformers = t3lib_div::trimExplode(',', $dbTransformers);
		}
		if (is_array($dbTransformers)) {
			foreach ($dbTransformers as $transformer) {
				$transformerObject = tx_feuserregister_TransformerFactory::getTransformer($transformer);
				if (is_array($this->_fieldConfiguration['transformerOptions.'][$transformerObject->getName().'.'])) {
					$transformerObject->setOptions($this->_fieldConfiguration['transformerOptions.'][$transformerObject->getName().'.']);
					$transformerObject->setType(tx_feuserregister_AbstractTransformer::TYPE_DATABASE);
					$this->_databaseTransformers[] = $transformerObject;
				}
			}
		}
		
		$htmlTransformers = $this->_fieldConfiguration['transformers.']['html'];
		if (strlen($htmlTransformers)) {
			$htmlTransformers = t3lib_div::trimExplode(',', $htmlTransformers);
		}
		if (is_array($htmlTransformers)) {
			foreach ($htmlTransformers as $transformer) {
				$transformerObject = tx_feuserregister_TransformerFactory::getTransformer($transformer);
				if (is_array($this->_fieldConfiguration['transformerOptions.'][$transformerObject->getName().'.'])) {
					$transformerObject->setOptions($this->_fieldConfiguration['transformerOptions.'][$transformerObject->getName().'.']);
					$transformerObject->setType(tx_feuserregister_AbstractTransformer::TYPE_HTML);
					$this->_htmlTransformers[] = $transformerObject;
				}
			}
		}
		
		switch ($this->_fieldConfiguration['type']) {
			case self::TYPE_HIDDEN:
				$this->_createHiddenField();
			break;
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
		$wrapConfig = (isset($this->_fieldConfiguration['errorWrap.'])) ? $this->_fieldConfiguration['errorWrap.'] : $this->_configuration['defaultWraps.']['error.'];
		if (is_array($wrapConfig) && strlen($this->_errorString) > 0) {
			return $this->_controller->cObj->stdWrap($this->_errorString, $wrapConfig);
		}
		return $this->_errorString;
	}
	
	public function getField() {
		$wrapConfig = (isset($this->_fieldConfiguration['fieldWrap.'])) ? $this->_fieldConfiguration['fieldWrap.'] : $this->_configuration['defaultWraps.']['fields.'];
		if (is_array($wrapConfig)) {
			return $this->_controller->cObj->stdWrap($this->_htmlField, $wrapConfig);
		}
		return $this->_htmlField;
	}
	
	public function getFieldName() {
		return $this->_fieldName;
	}
	
	public function getLabel() {
		$label = '';
		if ($this->_fieldConfiguration['type'] == 'TCA') {
			$label = $this->_tcaField->getLabel();
		} else {
			$label = $this->_localizationManager->getLL('label_field_'.$this->_fieldName);
		}
		
		if ($this->_configuration['global.']['useRequiredStringInLabel']) {
			$label .= ' ' . $this->getRequiredString();
		}
		
		$wrapConfig = (isset($this->_fieldConfiguration['labelWrap.'])) ? $this->_fieldConfiguration['labelWrap.'] : $this->_configuration['defaultWraps.']['fields.'];
		if (is_array($wrapConfig)) {
			$label = $this->_controller->cObj->stdWrap($label, $wrapConfig);
		}
		$label = str_replace('###FIELDNAME###', $this->_fieldName, $label);
		return $label;
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

	public function setValue($value) {
		$this->_value = $value;
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
	
	protected function _createHiddenField() {
		$value = $this->getValue(self::PARSE_HTML);
		$attributes = array();
		$attributes[] = "type=\"hidden\"";
		$attributes[] = "name=\"tx_feuserregister[data][{$this->_fieldName}]\"";
		$attributes[] = "value=\"{$value}\"";
		$attributes[] = "id=\"tx-feuserregister-field-{$this->_fieldName}\"";
		if ($this->_fieldConfiguration['additionalAttributes']) {
			$attributes[] = $this->_fieldConfiguration['additionalAttributes'];
		}
		
		$this->_htmlField = '<input '.implode(' ', $attributes).'/>';
	}
	
	protected function _createTextField() {
		$value = $this->getValue(self::PARSE_HTML);
		$attributes = array();
		$attributes[] = "type=\"text\"";
		$attributes[] = "name=\"tx_feuserregister[data][{$this->_fieldName}]\"";
		$attributes[] = "value=\"{$value}\"";
		$attributes[] = "id=\"tx-feuserregister-field-{$this->_fieldName}\"";
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
		$attributes[] = "id=\"tx-feuserregister-field-{$this->_fieldName}\"";
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
		$attributes[] = "id=\"tx-feuserregister-field-{$this->_fieldName}\"";
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
			case self::TYPE_HIDDEN:
					// we don't need to prepare this field type
			break;
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
			case self::TYPE_HIDDEN:
				$value = htmlspecialchars($value);
			break;
			case self::TYPE_PASSWORD:
				$value = htmlspecialchars($value);
			break;
			case self::TYPE_TCA:
				$value = $this->_tcaField->getValue(tx_feuserregister_AbstractTcaField::PARSE_HTML);
			break;
			case self::TYPE_TEXT:
				$value = htmlspecialchars($value);
			break;
			case self::TYPE_TEXTAREA:
				// we don't need to prepare this field type use transformer to
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
