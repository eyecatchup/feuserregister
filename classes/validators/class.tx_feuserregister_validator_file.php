<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Oliver Hader <oliver@typo3.org>
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

require_once(PATH_feuserregister . 'classes/validators/class.tx_feuserregister_abstractvalidator.php');

class tx_feuserregister_validator_File extends tx_feuserregister_AbstractValidator {
	protected $_name = 'file';

	/**
	 * @see tx_feuserregister_AbstractValidator::validate()
	 *
	 * @return boolean
	 */
	public function validate() {
		if (strlen($this->_value) == 0) {
			return TRUE;
		}

		if ($this->_value && $this->_options['types']) {
			$fileParts = t3lib_div::revExplode('.', strtolower($this->_value), 2);
			$fileTypes = t3lib_div::trimExplode(',', strtolower($this->_options['types']), TRUE);

			if (isset($fileParts[1]) && in_array($fileParts[1], $fileTypes)) {
				return TRUE;
			}
		}

		return FALSE;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_validator_dateformat.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_validator_dateformat.php']);
}

?>