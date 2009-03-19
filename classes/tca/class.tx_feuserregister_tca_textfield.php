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

class tx_feuserregister_tca_TextField extends tx_feuserregister_AbstractTcaField {

	public function getHtmlField() {
		if ($this->_configuration['config']['cols']) {
			$this->_attributes['cols'] = $this->_configuration['config']['cols'];
		}
		if ($this->_configuration['config']['rows']) {
			$this->_attributes['rows'] = $this->_configuration['config']['rows'];
		}
		if ($this->_configuration['config']['wrap'] == 'off') {
			$this->_attributes['wrap'] = 'off';
		}
		
		$value = $this->getValue(self::PARSE_HTML);
		$element = "<textarea{$this->_getAttributesString()}>{$value}</textarea>";
		return $element;
	}
	
	protected function _prepareForDatabase() {
		return $this->_value;
	}

	protected function _prepareForHtml() {
		return nl2br(htmlspecialchars($this->_value));
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_tca_textfield.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_tca_textfield.php']);
}

?>