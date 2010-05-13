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

class tx_feuserregister_ValidatorFactory {
	const VALIDATOR_AGE			= 'age';
	const VALIDATOR_BETWEEN		= 'between';
	const VALIDATOR_BLACKLIST	= 'blacklist';
	const VALIDATOR_BOOLEAN		= 'boolean';
	const VALIDATOR_DATEFORMAT	= 'dateformat';
	const VALIDATOR_EMAIL		= 'email';
	const VALIDATOR_EQUALFIELD	= 'equalField';
	const VALIDATOR_EQUALVALUE	= 'equalValue';
	const VALIDATOR_FLOAT		= 'float';
	const VALIDATOR_INT			= 'int';
	const VALIDATOR_IP			= 'ip';
	const VALIDATOR_IPV4		= 'ipv4';
	const VALIDATOR_IPV6		= 'ipv6';
	const VALIDATOR_LENGTH		= 'length';
	const VALIDATOR_REGEXP		= 'regexp';
	const VALIDATOR_REQUIRED	= 'required';
	const VALIDATOR_UNIQUEINDB	= 'uniqueInDb';
	const VALIDATOR_UNIQUEINPID	= 'uniqueInPid';
	const VALIDATOR_URL 		= 'url';
	const VALIDATOR_FILE		= 'file';

	static public function getValidator($typ) {
		switch ($typ) {
			case self::VALIDATOR_AGE:
				$validatorClass = 'tx_feuserregister_validator_Age';
			break;
			case self::VALIDATOR_BETWEEN:
				$validatorClass = 'tx_feuserregister_validator_Between';
			break;
			case self::VALIDATOR_BLACKLIST:
				$validatorClass = 'tx_feuserregister_validator_Blacklist';
			break;
			case self::VALIDATOR_BOOLEAN:
				$validatorClass = 'tx_feuserregister_validator_Boolean';
			break;
			case self::VALIDATOR_DATEFORMAT:
				$validatorClass = 'tx_feuserregister_validator_Dateformat';
			break;
			case self::VALIDATOR_EMAIL:
				$validatorClass = 'tx_feuserregister_validator_Email';
			break;
			case self::VALIDATOR_EQUALFIELD:
				$validatorClass = 'tx_feuserregister_validator_EqualField';
			break;
			case self::VALIDATOR_EQUALVALUE:
				$validatorClass = 'tx_feuserregister_validator_EqualValue';
			break;
			case self::VALIDATOR_FLOAT:
				$validatorClass = 'tx_feuserregister_validator_float';
			break;
			case self::VALIDATOR_INT:
				$validatorClass = 'tx_feuserregister_validator_Int';
			break;
			case self::VALIDATOR_IP:
				$validatorClass = 'tx_feuserregister_validator_Ip';
			break;
			case self::VALIDATOR_IPV4:
				$validatorClass = 'tx_feuserregister_validator_Ipv4';
			break;
			case self::VALIDATOR_IPV6:
				$validatorClass = 'tx_feuserregister_validator_Ipv6';
			break;
			case self::VALIDATOR_LENGTH:
				$validatorClass = 'tx_feuserregister_validator_Length';
			break;
			case self::VALIDATOR_REGEXP:
				$validatorClass = 'tx_feuserregister_validator_Regexp';
			break;
			case self::VALIDATOR_REQUIRED:
				$validatorClass = 'tx_feuserregister_validator_Required';
			break;
			case self::VALIDATOR_UNIQUEINDB:
				$validatorClass = 'tx_feuserregister_validator_UniqueInDb';
			break;
			case self::VALIDATOR_UNIQUEINPID:
				$validatorClass = 'tx_feuserregister_validator_UniqueInPid';
			break;
			case self::VALIDATOR_URL:
				$validatorClass = 'tx_feuserregister_validator_Url';
			break;
			case self::VALIDATOR_FILE:
				$validatorClass = 'tx_feuserregister_validator_File';
			break;
			default:
				$validatorClass = $typ;
			break;
		}
		if (!class_exists($validatorClass)) {
			$classFile = t3lib_extMgm::extPath('feuserregister').'classes/validators/class.'.strtolower($validatorClass).'.php';
			if (file_exists($classFile)) {
				require_once($classFile);
			}
			if (!class_exists($validatorClass)) {
				die ($validatorClass . ' not found!');
			}
		}
		$class = t3lib_div::makeInstance($validatorClass);
		return $class;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_validatorfactory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_validatorfactory.php']);
}

?>