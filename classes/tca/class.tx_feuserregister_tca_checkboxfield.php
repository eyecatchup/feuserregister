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
 * $Id: class.tx_feuserregister_tca_checkboxfield.php 358 2009-03-19 13:55:27Z franae $
 */

require_once(PATH_feuserregister . 'classes/tca/class.tx_feuserregister_abstracttcafield.php');

class tx_feuserregister_tca_CheckboxField extends tx_feuserregister_AbstractTcaField {

	public function getHtmlField() {
		$this->_attributes['type'] = 'checkbox';
		$this->_attributes['value'] = $this->_value;
		if (strlen($this->_value) == 0) {
			$this->_value = $this->_configuration['config']['default'];
		}
		if ($this->_configuration['config']['items']) {
			$this->_attributes['name'] = $this->_attributes['name'] . '[]';
			$elements = array();
			foreach ($this->_configuration['config']['items'] as $item) {
				$id = $this->_attributes['id'];
				$counter = count($elements)+1;
				$this->_attributes['value'] = $item[1];
				$this->_attributes['id'] = $this->_attributes['id'] . "-{$counter}";
				$label = $this->_getLllValue($item[0]);
				if ($this->_value & pow(2,$counter-1)) {
					$this->_attributes['checked'] =  'checked';
				} else {
					unset($this->_attributes['checked']);
				}
				
				$elements[] = "<input{$this->_getAttributesString()} /> <label for=\"{$this->_attributes['id']}\">{$label}</label>";
				$this->_attributes['id'] = $id;
			}
			return implode(' ', $elements);
		} else {
			if ($this->_value) {
				$this->_attributes['checked'] = 'checked';
			}
			$element = "<input{$this->_getAttributesString()} />";
			return $element;
		}
	}
	
	protected function _prepareForDatabase() {
		return $this->_value;
	}

	protected function _prepareForHtml() {
		if ($this->_configuration['config']['items']) {
			$elements = array();
			$counter = 0;
			foreach ($this->_configuration['config']['items'] as $item) {
				if ($this->_value & pow(2,$counter)) {
					$elements[] = $this->_getLllValue($item[0]);
				}
				$counter++;
			}
			return htmlspecialchars(implode(', ', $elements));
		} else {
			if ($this->_value) {
				return htmlspecialchars($this->getLabel());
			}
		}
		return '';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_tca_ceckboxfield.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_tca_ceckboxfield.php']);
}

?>