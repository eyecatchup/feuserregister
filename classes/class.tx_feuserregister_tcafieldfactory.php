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

class tx_feuserregister_TcaFieldFactory {
	const FIELD_CHECK			= 'check';
	const FIELD_INPUT			= 'input';
	const FIELD_RADIO			= 'radio';
	const FIELD_SELECT			= 'select';
	const FIELD_TEXT			= 'text';
	
	static public function getTcaField($type) {
		switch ($type) {
			case self::FIELD_CHECK:
				$tcaFieldClass = 'tx_feuserregister_tca_CheckboxField';
			break;
			case self::FIELD_INPUT:
				$tcaFieldClass = 'tx_feuserregister_tca_InputField';
			break;
			case self::FIELD_RADIO:
				$tcaFieldClass = 'tx_feuserregister_tca_RadioField';
			break;
			case self::FIELD_SELECT:
				$tcaFieldClass = 'tx_feuserregister_tca_SelectField';
			break;
			case self::FIELD_TEXT:
				$tcaFieldClass = 'tx_feuserregister_tca_TextField';
			break;
			default:
				$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_Tca');
				throw new $exceptionClass("no support for TCA field type '{$type}'");
			break;
		}
		if (!class_exists($tcaFieldClass)) {
			$classFile = t3lib_extMgm::extPath('feuserregister').'classes/tca/class.'.strtolower($tcaFieldClass).'.php';
			if (file_exists($classFile)) {
				require_once($classFile);	
			}
			if (!class_exists($tcaFieldClass)) {
				die ($tcaFieldClass . ' not found!');
			}
		}
		$class = t3lib_div::makeInstance($tcaFieldClass);
		return $class;
	}	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_tcafieldfactory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_tcafieldfactory.php']);
}

?>