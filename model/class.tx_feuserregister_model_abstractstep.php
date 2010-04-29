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

abstract class tx_feuserregister_model_AbstractStep {
	protected $_configuration = null;
	/**
	 * @var tx_feuserregister_controller_UserRegistration
	 */
	protected $_controller = null;
	protected $_fields = array();
	protected $_templateContent = '';
	protected $_request = null;
	protected $_stepName = '';
	protected $_validationActive = true;

	public function __construct($stepName, $configuration, $mode) {
		$this->_configuration = $configuration;
		$this->_controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');
		$this->_stepName = $stepName;
		if (strlen($this->_configuration['fields'])) {
			$fields = t3lib_div::trimExplode(',', $this->_configuration['fields']);
			if (is_array($fields)) {
				foreach ( $fields as $field ) {
					$className = t3lib_div::makeInstanceClassName('tx_feuserregister_model_Field');
					$this->_fields[] = new $className($field);
				}
			}
		}
		$this->_request = t3lib_div::makeInstance('tx_feuserregister_Request');
		
		$mode = tx_feuserregister_Registry::get('tx_feuserregister_mode');
		$template = (strlen($mode)) ? $mode : 'register';
		$this->_templateContent = $this->_controller->cObj->fileResource($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']['templates.'][$template]);
		$this->_templateContent = t3lib_parsehtml::getSubpart($this->_templateContent, '###' . strtoupper($this->_configuration['subpart']) . '###');
	}

	public function getFields() {
		return $this->_fields;
	}

	abstract public function render();

	public function clearData() {
		tx_feuserregister_SessionRegistry::set('tx_feuserregister_feuser', null);
		tx_feuserregister_SessionRegistry::set('tx_feuserregister_currentFeuser', null);
		tx_feuserregister_SessionRegistry::set('tx_feuserregister_sessionuser', null);
	}

	public function setValidate($active) {
		$this->_validationActive = $active;
	}

	public function storeData() {
		/* @var $sessionUser tx_feuserregister_model_SessionUser */
		$sessionUser = t3lib_div::makeInstance('tx_feuserregister_model_SessionUser');
		foreach ( $this->_fields as $field ) {
			$sessionUser->set($field->getFieldName(), $field->getValue());
		}
		$sessionUser->storeData();
	}

	public function isValid() {
		foreach ( $this->_fields as $field ) {
			if (! $field->validate()) {
				return false;
			}
		}
		return true;
	}

	protected function _validate() {
		foreach ( $this->_fields as $field ) {
			$field->validate();
		}
	}

	protected function _getFieldMarker() {
		$marker = array();
		foreach ( $this->_fields as $field ) {
			$marker['###FIELD_' . $field->getFieldName() . '###'] = $field->getField();
		}
		return $marker;
	}

	protected function _getLabelmarker() {
		$marker = array();
		foreach ( $this->_fields as $field ) {
			$marker['###LABEL_' . $field->getFieldName() . '###'] = $field->getLabel();
		}
		return $marker;
	}

	protected function _getValueMarker() {
		$marker = array();
		foreach ($this->_fields as $field) {
			$marker['###VALUE_' . $field->getFieldName() . '###'] = $field->getValue(tx_feuserregister_model_Field::PARSE_HTML);
		}
		return $marker;
	}

	protected function _getRequiredMarker() {
		$marker = array();
		foreach ( $this->_fields as $field ) {
			$marker['###REQUIRED_' . $field->getFieldName() . '###'] = $field->getRequiredString();
		}
		return $marker;
	}

	protected function _getErrorMarker() {
		$marker = array();
		foreach ( $this->_fields as $field ) {
			$marker['###ERROR_' . $field->getFieldName() . '###'] = $field->getErrorString();
		}
		return $marker;
	}

	protected function _getGlobalMarker() {
		$marker = array(
			'###FORM_URL###' 		=> $this->_controller->cObj->typoLink_URL(array('parameter' => $GLOBALS['TSFE']->id)), 
			'###HIDDEN_FIELDS###' 	=> $this->_getHiddenFields()
		);
		$this->_controller->notifyObservers('globalMarker', array(
			'stepName'		=> $this->_stepName,
			'markerArray'	=> &$marker
		));
		return $marker;
	}
	
	protected function _getLllMarker() {
		$localizationManager = tx_feuserregister_LocalizationManager::getInstance(
			'EXT:feuserregister/lang/locallang_fields.xml', 
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']
		);
		return $localizationManager->getAllAsMarkerArray();
	}

	protected function _getHiddenFields() {
		return '
			<input type="hidden" value="nextStep" name="tx_feuserregister[action]" />
			<input type="hidden" value="' . $this->_stepName . '" name="tx_feuserregister[step]" />
		';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_abstractstep.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_abstractstep.php']);
}

?>