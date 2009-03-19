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
 * $Id: class.tx_feuserregister_tca_inputfield.php 352 2009-03-19 12:31:58Z franae $
 */

require_once(PATH_feuserregister . 'classes/tca/class.tx_feuserregister_abstracttcafield.php');

class tx_feuserregister_tca_InputField extends tx_feuserregister_AbstractTcaField {

	public function getHtmlField() {
		$this->_attributes['type'] = 'text';
		$this->_attributes['value'] = $this->getValue(self::PARSE_HTML);
		if ($this->_configuration['config']['max']) {
			$this->_attributes['maxlength'] = $this->_configuration['config']['max'];
		}
		if ($this->_configuration['config']['eval']) {
			$classes = str_replace(',', ' ', $this->_configuration['config']['eval']);
			$this->_attributes['class'] = $classes;
		}
		
		$element = "<input{$this->_getAttributesString()} />";
		return $element;
	}
	
	protected function _prepareForDatabase() {
		return $this->_value;
	}

	protected function _prepareForHtml() {
		return htmlspecialchars($this->_value);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_tca_inputfield.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_tca_inputfield.php']);
}

?>