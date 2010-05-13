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

require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_abstractstep.php');

class tx_feuserregister_model_Preview extends tx_feuserregister_model_AbstractStep {
	protected $_stepName 	= 'preview';
	protected $_steps		= array();

	public function render() {
		$fieldMarker	= $this->_getFieldMarker();
		$labelMarker	= $this->_getLabelmarker();
		$valueMarker	= $this->_getValueMarker();
		$globalMarker	= $this->_getGlobalMarker();
		$lllMarker		= $this->_getLllMarker();
	
		$marker = array_merge($fieldMarker, $labelMarker, $valueMarker, $globalMarker, $lllMarker);

		$this->_controller->notifyObservers('renderPreviewAdditionalMarker', array('marker' => &$marker));
	
		return t3lib_parsehtml::substituteMarkerArray($this->_templateContent, $marker, '', 0, 1);
	}

	public function setSteps(array $steps) {
		array_shift($steps);
		$this->_steps = $steps;
	}

	protected function _getFieldMarker() {
		$marker = array();
		foreach ($this->_steps as $step) {
			$fields = $step->getFields();
			foreach ($fields as $field) {
				$marker['###FIELD_'.$field->getFieldName().'###'] = $field->getValue(tx_feuserregister_model_Field::PARSE_HTML);
			}
		}
		return $marker;
	}

	protected function _getLabelmarker() {
		$marker = array();
		foreach ($this->_steps as $step) {
			$fields = $step->getFields();
			foreach ($fields as $field) {
				$marker['###LABEL_'.$field->getFieldName().'###'] = $field->getLabel();
			}
		}
		return $marker;
	}

	protected function _getValueMarker() {
		$marker = array();
		foreach ($this->_steps as $step) {
			$fields = $step->getFields();
			foreach ($fields as $field) {
				$marker['###VALUE_' . $field->getFieldName() . '###'] = $field->getValue(tx_feuserregister_model_Field::PARSE_HTML);
			}
		}
		return $marker;
	}

	protected function _getGlobalMarker() {
		$marker = array(
			'###FORM_URL###' 		=> $this->_controller->cObj->typoLink_URL(array('parameter' => $GLOBALS['TSFE']->id)),
			'###HIDDEN_FIELDS###'		=> $this->_getHiddenFields()
		);
		return $marker;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_preview.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_preview.php']);
}

?>