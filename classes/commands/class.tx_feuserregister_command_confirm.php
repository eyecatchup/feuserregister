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
require_once(PATH_feuserregister . 'interfaces/interface.tx_feuserregister_interface_command.php');

class tx_feuserregister_command_Confirm implements tx_feuserregister_interface_Command {
	const MODE = 'confirm';

	protected $_configuration = null;
	protected $_controller = null;
	protected $_request = null;
	protected $_templateContent = '';

	public function __construct() {
		$this->_request = t3lib_div::makeInstance('tx_feuserregister_Request');
		$this->_configuration = tx_feuserregister_Registry::get('tx_feuserregister_configuration');
		$this->_controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');

		$this->_templateContent = $this->_controller->cObj->fileResource($this->_configuration['templates.']['confirm']);
		$this->_templateContent = t3lib_parsehtml::getSubpart($this->_templateContent, '###TEMPLATE_SUCCESS###');
	
		tx_feuserregister_Registry::set('tx_feuserregister_mode', self::MODE);
	}

	
	/**
	 * @see tx_feuserregister_interface_Command::execute()
	 *
	 */
	public function execute() {
		$this->_controller->notifyObservers('onConfirmStart');
		$hashCode = $GLOBALS['TYPO3_DB']->quoteStr($this->_request->get('confirmationCode'), 'fe_users');
		$feuser = t3lib_div::makeInstance('tx_feuserregister_model_FeUser');
		$feuser->select("md5(concat(uid,crdate,'{$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']}')) = '{$hashCode}' AND disable = 1");
		if ($feuser->uid) {
			if ($this->_configuration['global.']['userGroupsAfterConfirmation']) {
				$feuser->set('usergroup', $this->_configuration['global.']['userGroupsAfterConfirmation']);
			}
			if (!$this->_configuration['global.']['adminConfirmation']) {
				$feuser->set('disable', 0);
				if ($feuser->save()) {
					$marker = $this->_createGlobalMarker();
					$this->_controller->notifyObservers('onConfirmSaveSuccess', array(
						'feuser' => $feuser,
						'marker' => &$marker
					));
					if ($this->_configuration['global.']['adminEmail.']['onConfirmation']) {
						tx_feuserregister_Mailer::send('admin', 'onconfirmation', $marker);
					}
					if ($this->_configuration['global.']['userEmail.']['onConfirmation']) {
						tx_feuserregister_Mailer::send('user', 'onconfirmation', $marker);
					}
					return t3lib_parsehtml::substituteMarkerArray($this->_templateContent, $marker, '', 0, 1);
				} else {
					$exception = t3lib_div::makeInstance('tx_feuserregister_exception_Database', 'error while saving fe user', 1300);
					throw $exception;
				}
			}
		} else {
			$feuser->select("md5(concat(uid,crdate,tx_feuserregister_temporarydata,'{$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']}')) = '{$hashCode}'");
			if ($feuser->uid) {
				if ($this->_configuration['global.']['userGroupsAfterUpdateConfirmation']) {
					$feuser->set('usergroup', $this->_configuration['global.']['userGroupsAfterUpdateConfirmation']);
				}
				$newValues = unserialize($feuser->get('tx_feuserregister_temporarydata'));
				if (is_array($newValues)) {
					foreach ($newValues as $key => $value) {
						$feuser->set($key, $value);
					}
					$feuser->set('tx_feuserregister_temporarydata', '');
					if ($feuser->save()) {
						$marker = $this->_createGlobalMarker();
						$this->_controller->notifyObservers('onUpdateSaveSuccess', array(
							'feuser' => $feuser,
							'marker' => &$marker
						));
						if ($this->_configuration['global.']['adminEmail.']['onConfirmation']) {
							tx_feuserregister_Mailer::send('admin', 'onconfirmation', $marker);
						}
						if ($this->_configuration['global.']['userEmail.']['onConfirmation']) {
							tx_feuserregister_Mailer::send('user', 'onconfirmation', $marker);
						}
						return t3lib_parsehtml::substituteMarkerArray($this->_templateContent, $marker, '', 0, 1);
					} else {
						$exception = t3lib_div::makeInstance('tx_feuserregister_exception_Database', 'error while saving fe user', 1300);
						throw $exception;
					}
				} else {
					$exception = t3lib_div::makeInstance('tx_feuserregister_exception_Confirm', 'wrong hash code or user always confirmed', 1400);
					throw $exception;
				}
			} else {
				$exception = t3lib_div::makeInstance('tx_feuserregister_exception_Confirm', 'wrong hash code or user always confirmed', 1400);
				throw $exception;
			}
		}
		$this->_controller->notifyObservers('onConfirmEnd');
	}

	public function getMode() {
		return self::MODE;
	}

	protected function _createGlobalMarker() {
		$marker = array();
		$localizationManager = tx_feuserregister_LocalizationManager::getInstance(
			'EXT:feuserregister/lang/locallang_db.xml',
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']
		);
		$marker = $localizationManager->getAllAsMarkerArray();
		$this->_controller->notifyObservers('globalMarkerConfirm', array(
			'markerArray'	=> &$marker
		));
		return $marker;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/commands/class.tx_feuserregister_command_confirm.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/commands/class.tx_feuserregister_command_confirm.php']);
}

?>