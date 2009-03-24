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

require_once(PATH_feuserregister . 'classes/validators/class.tx_feuserregister_abstractvalidator.php');


class tx_feuserregister_validator_EqualField extends tx_feuserregister_AbstractValidator {
	protected $_name = 'equalField';
	
	/**
	 * @see tx_feuserregister_AbstractValidator::validate()
	 *
	 * @return boolean
	 */
	public function validate() {
		$request = t3lib_div::makeInstance('tx_feuserregister_Request');
		$requestData = $request->get('data');
		$this->_errorMessage = str_replace('###FIELDS###', $this->_options['fieldList'], $this->_errorMessage);
		
		$fields = t3lib_div::trimExplode(',', $this->_options['fieldList']);
		
		$result = true;
		if (is_array($fields)) {
			foreach ($fields as $field) {
				if (isset($this->_options['negate'])) {
					if (strcmp($this->_value, $requestData[$field]) === 0) {
						$result = false;
						break;
					}
				} else {
					if (strcmp($this->_value, $requestData[$field]) !== 0) {
						$result = false;
						break;
					}
				} 
			}
		}
		
		return $result;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_validator_equalfield.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_validator_equalfield.php']);
}

?>