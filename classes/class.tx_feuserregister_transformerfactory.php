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

class tx_feuserregister_TransformerFactory {
	const TRANSFORMER_BR2NL				= 'br2nl';
	const TRANSFORMER_DATE				= 'date';
	const TRANSFORMER_HTMLSPECIALCHARS	= 'htmlspecialchars';
	const TRANSFORMER_MD5				= 'md5';
	const TRANSFORMER_NL2BR				= 'nl2br';
	const TRANSFORMER_STRIPTAGS			= 'striptags';
	const TRANSFORMER_T3SEC_SALTEDPW	= 't3sec_saltedpw';
	const TRANSFORMER_TIMESTAMP			= 'timestamp';
	
	static public function getTransformer($typ) {
		switch ($typ) {
			case self::TRANSFORMER_BR2NL:
				$transformerClass = 'tx_feuserregister_transformer_Br2Nl';
			break;
			case self::TRANSFORMER_DATE:
				$transformerClass = 'tx_feuserregister_transformer_Date';
			break;
			case self::TRANSFORMER_HTMLSPECIALCHARS:
				$transformerClass = 'tx_feuserregister_transformer_Htmlspecialchars';
			break;
			case self::TRANSFORMER_MD5:
				$transformerClass = 'tx_feuserregister_transformer_Md5';
			break;
			case self::TRANSFORMER_NL2BR:
				$transformerClass = 'tx_feuserregister_transformer_Nl2Br';
			break;
			case self::TRANSFORMER_STRIPTAGS:
				$transformerClass = 'tx_feuserregister_transformer_StripTags';
			break;
			case self::TRANSFORMER_T3SEC_SALTEDPW:
				$transformerClass = 'tx_feuserregister_transformer_T3sec_saltedpw';
			break;
			case self::TRANSFORMER_TIMESTAMP:
				$transformerClass = 'tx_feuserregister_transformer_Timestamp';
			break;
			default:
				$transformerClass = $typ;
			break;
		}
		if (!class_exists($transformerClass)) {
			$classFile = t3lib_extMgm::extPath('feuserregister').'classes/transformer/class.'.strtolower($transformerClass).'.php';
			if (file_exists($classFile)) {
				require_once($classFile);	
			}
			if (!class_exists($transformerClass)) {
				die ($transformerClass . ' not found!');
			}
		}
		$class = t3lib_div::makeInstance($transformerClass);
		return $class;
	}	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_transformerfactory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_transformerfactory.php']);
}

?>