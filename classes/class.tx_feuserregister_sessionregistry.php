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

class tx_feuserregister_SessionRegistry extends tx_feuserregister_Registry {
	const SESSION_SPACE = 'ses';

	/**
	 * getter method.
	 *
	 * @param string $index - get the value associated with $index
	 * @return mixed
	 */
	public static function get($index) {
		return unserialize($GLOBALS['TSFE']->fe_user->getKey(self::SESSION_SPACE, $index));
	}

	/**
	 * setter method.
	 *
	 * @param string $index - set the $index for the given $value
	 * @param mixed $value - set the $value for the given $index
 	 * @return mixed
	 */
	public static function set($index, $value) {
		$GLOBALS['TSFE']->fe_user->setKey(self::SESSION_SPACE, $index, serialize($value));
		$GLOBALS['TSFE']->fe_user->storeSessionData();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_sessionregistry.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_sessionregistry.php']);
}

?>