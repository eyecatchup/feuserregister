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

require_once(PATH_feuserregister . 'classes/tca/class.tx_feuserregister_abstracttcafield.php');

class tx_feuserregister_tca_SelectField extends tx_feuserregister_AbstractTcaField {

	public function getHtmlField() {
		if ($this->_configuration['config']['size'] > 1) {
			$this->_attributes['size'] = $this->_configuration['config']['size'];
		}
		if ($this->_configuration['config']['maxitems'] > 1) {
			$this->_attributes['multiple'] = 'multiple';
			$this->_attributes['name'] = $this->_attributes['name'] . '[]';
		}
		
		$htmlOptions = array();
		if (is_array($this->_configuration['config']['items'])) {
			foreach ($this->_configuration['config']['items'] as $option) {
				$selected = ($this->_value == $option[1]) ? ' selected="selected"' : '';
				$label = $this->_getLllValue($option[0]);
				$htmlOptions[] = "<option value=\"{$option[1]}\"{$selected}>{$label}</option>";
			}
		}
		if ($this->_configuration['config']['foreign_table']) {
			$labelField = $GLOBALS['TCA'][$this->_configuration['config']['foreign_table']]['ctrl']['label'];
			$options = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				"uid,{$labelField}",
				$this->_configuration['config']['foreign_table'],
				'1=1' . $this->_configuration['config']['foreign_where']
			);
			foreach ($options as $option) {
				if (is_array($this->_value)) {
					$selected = (in_array($option['uid'], $this->_value)) ? ' selected="selected"' : '';
				}
				$htmlOptions[] = "<option value=\"{$option['uid']}\"{$selected}>{$option[$labelField]}</option>";
			}
		}
		$htmlOptions = implode("\n", $htmlOptions);
		
		$element = "<select{$this->_getAttributesString()}>\n{$htmlOptions}</select>";
		return $element;
	}
	
	protected function _prepareForDatabase() {
		return (is_array($this->_value)) ? implode(',', $this->_value) : $this->_value;
	}

	/**
	 * prepare value for html output
	 *
	 * @return string
	 */
	protected function _prepareForHtml() {
		$options = $this->_configuration['config']['items'];
		if (is_array($options)) {
			foreach ($options as $option) {
				if ($this->_value == $option[1]) {
					return $this->_getLllValue($option[0]);
				}
			}
			return '';
		}
		if ($this->_configuration['config']['foreign_table']) {
			$labelField = $GLOBALS['TCA'][$this->_configuration['config']['foreign_table']]['ctrl']['label'];
			$options = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				"uid,{$labelField}",
				$this->_configuration['config']['foreign_table'],
				'1=1' . $this->_configuration['config']['foreign_where']
			);
			$values = array();
			foreach ($options as $option) {
				if (is_array($this->_value)) {
					if (in_array($option['uid'], $this->_value)) {
						$values[] = $option[$labelField];
					}
				}
			}
			return implode(', ', $values);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_tca_selectfield.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_tca_selectfield.php']);
}

?>