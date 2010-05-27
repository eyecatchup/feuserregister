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
 * $Id: class.tx_feuserregister_userregistration_wizicon.php 33161 2010-05-13 10:27:14Z ohader $
 */

/**
 * Class that adds the wizard icon.
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage feuserregister
 */
class tx_feuserregister_userregistration_wizicon {
	/**
	 * Processing the wizard items array
	 *
	 * @param    array        $wizardItems: The wizard items
	 * @return    Modified array with wizard items
	 */
	function proc($wizardItems) {
		global $LANG;
	
		$LL = $this->includeLocalLang();
	
		$tmpWizardItems = array();
		foreach ($wizardItems as $wizardId => $wizardConfig) {
			$tmpWizardItems[$wizardId] = $wizardConfig;
			if ($wizardId == 'forms') {
				$tmpWizardItems['forms_feuserregister_UserRegistration'] = array(
					'icon'			=> t3lib_extMgm::extRelPath('feuserregister') . 'feuserregister_form.gif',
					'title'			=> $LANG->getLLL('tt_content.list_type_userRegistration_title', $LL),
					'description'	=> $LANG->getLLL('tt_content.list_type_userRegistration_plus_wiz_description', $LL),
					'params'		=> '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=feuserregister_UserRegistration'
				);
			}
		}
	
		return $tmpWizardItems;
	}

	/**
	 * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array found in that file.
	 *
	 * @return    The array with language labels
	 */
	function includeLocalLang() {
		$llFile = t3lib_extMgm::extPath('feuserregister') . 'lang/locallang_db.xml';
		$LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS['LANG']->lang);
	
		return $LOCAL_LANG;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/class.tx_feuserregister_userregistration_wizicon.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/class.tx_feuserregister_userregistration_wizicon.php']);
}

?>