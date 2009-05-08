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
 * $Id: class.tx_feuserregister_exception_confirm.php 18089 2009-03-19 23:03:02Z neoblack $
 */

require_once(t3lib_extMgm::extPath('feuserregister') . 'classes/class.tx_feuserregister_sessionregistry.php');

class tx_feuserregister_t3libauth_hook {

	function logoff_pre_processing (&$params, &$pObj) {
		//Is it realy logout?
		if ($pObj->loginType === 'FE' && t3lib_div::_GP('logintype') === 'logout') {
			tx_feuserregister_SessionRegistry::set('tx_feuserregister_feuser', null);
			tx_feuserregister_SessionRegistry::set('tx_feuserregister_currentFeuser', null);
			tx_feuserregister_SessionRegistry::set('tx_feuserregister_sessionuser', null);
		}
	}
}


?>