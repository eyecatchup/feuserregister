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

class tx_feuserregister_validator_UniqueInPid extends tx_feuserregister_AbstractValidator {
	protected $_name = 'uniqueInPid';
	
	/**
	 * @see tx_feuserregister_AbstractValidator::validate()
	 *
	 * @return boolean
	 */
	public function validate() {
		$feuserClassName	= t3lib_div::makeInstance('tx_feuserregister_model_FeUser');
		$pageSelect			= t3lib_div::makeInstance('t3lib_pageSelect');
		
		$res = $GLOBALS ['TYPO3_DB']->sql_query('describe fe_users');
		while ($data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$definitions[$data['Field']] = $data['Type'];
		}
		$value = (substr_count($definitions[$this->_fieldname], 'int') > 0) ? intval($this->_value) : $GLOBALS['TYPO3_DB']->fullQuoteStr($this->_value, 'fe_users');
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'fe_users',
			'pid = ' . $this->_options['pid'] . ' AND ' . $this->_fieldname . ' = ' . $value . $pageSelect->enableFields('fe_users')
		);
		$result = ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) ? false : true;

		$currentFeuser = tx_feuserregister_SessionRegistry::get('tx_feuserregister_currentFeuser');
		if ($currentFeuser instanceof $feuserClassName) {
			if ($currentFeuser->get($this->_fieldname) === $this->_value) {
				$result = true;
			}
		}
		return $result;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_validator_uniqueinpid.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_validator_uniqueinpid.php']);
}

?>