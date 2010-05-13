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
 * $Id: class.tx_feuserregister_validator_length.php 352 2009-03-19 12:31:58Z franae $
 */

require_once(PATH_feuserregister . 'classes/validators/class.tx_feuserregister_abstractvalidator.php');

class tx_feuserregister_validator_Age extends tx_feuserregister_AbstractValidator {
	protected $_name = 'age';

	/**
	 * @see tx_feuserregister_AbstractValidator::validate()
	 *
	 * @return boolean
	 */
	public function validate() {
		$this->_errorMessage = str_replace('###MINAGE###', $this->_options['minAge'], $this->_errorMessage);
		$this->_errorMessage = str_replace('###MAXAGE###', $this->_options['maxAge'], $this->_errorMessage);
	
		$value = $this->_value;
		if (isset($this->_options['format'])) {
			$transformer = t3lib_div::makeInstance('tx_feuserregister_transformer_Timestamp');
			$transformer->setValue($this->_value);
			$transformer->setOptions(array(
				'inFormat'	=> $this->_options['format']
			));
			$value = $transformer->transform();
		}

		if ($this->_options['minAge'] && $this->_options['maxAge']) {
			$minDate = strtotime($this->_options['minAge']);
			$maxDate = strtotime($this->_options['maxAge']);
			return ($value <= $minDate && $value >= $maxDate) ? true : false;
		}
		if ($this->_options['minAge']) {
			$minDate = strtotime($this->_options['minAge']);
			return ($value <= $minDate) ? true : false;
		}
		if ($this->_options['maxAge']) {
			$maxDate = strtotime($this->_options['maxAge']);
			return ($value >= $maxDate) ? true : false;
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_validator_age.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_validator_age.php']);
}

?>
