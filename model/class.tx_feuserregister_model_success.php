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

require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_abstractstep.php');

class tx_feuserregister_model_Success extends tx_feuserregister_model_AbstractStep {
	
	public function render() {
		$this->_configuration = tx_feuserregister_Registry::get('tx_feuserregister_configuration');
		
		$fieldMarker	= $this->_getFieldMarker();
		$labelMarker	= $this->_getLabelmarker();
		$globalMarker	= $this->_getGlobalMarker();
		$lllMarker		= $this->_getLllMarker();
		
		$marker = array_merge($fieldMarker, $labelMarker, $globalMarker, $lllMarker);

		$this->_controller->notifyObservers('renderSuccessAdditionalMarker', array('marker' => &$marker));
		
		$allFields = array();
		foreach ($this->_steps as $step) {
			$fields = $step->getFields();
			foreach ($fields as $field) {
				$allFields[] = $field;
			}
		}
		
		$reloadHash_session = tx_feuserregister_SessionRegistry::get('tx_feuserregister_reloadhash');
		$reloadHash = md5(serialize($allFields));
		
		if ($reloadHash_session === null || $reloadHash !== $reloadHash_session) {
			$mode = tx_feuserregister_Registry::get('tx_feuserregister_mode');
			if ($mode === 'edit') {
				return $this->_processEdit($allFields, $marker, $reloadHash);
			}
			if ($mode === 'register') {
				return $this->_processRegister($allFields, $marker,$reloadHash);
			}
		} else {
			$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_StepManager');
			throw new $exceptionClass('second reload', 3300);
		}
	}
	
	protected function _processEdit($allFields, $marker, $reloadHash) {
		/* @var $feuser tx_feuserregister_model_FeUser */
		$feuser = tx_feuserregister_SessionRegistry::get('tx_feuserregister_feuser');
		/* @var $currentFeuser tx_feuserregister_model_FeUser */
		$currentFeuser = tx_feuserregister_SessionRegistry::get('tx_feuserregister_currentFeuser');
		$confirmFields = t3lib_div::trimExplode(',', $this->_configuration['global.']['confirmationOnUpdateFields']);					
		$confirmValues = array();
		foreach ($allFields as $field) {
			if (in_array($field->getFieldname(), $confirmFields)) {
				if ($currentFeuser->get($field->getFieldname()) !== $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE)) {
					$confirmValues[$field->getFieldname()] = $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE);
					if ($this->_configuration['global.']['useFieldAsUsername'] && $this->_configuration['global.']['useFieldAsUsername'] === $field->getFieldName()) {
						$feuser->set('username', $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE));
						$confirmValues['username'] = $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE);
					}
				} else {
					if ($this->_configuration['global.']['useFieldAsUsername'] && $this->_configuration['global.']['useFieldAsUsername'] === $field->getFieldName()) {
						$feuser->set('username', $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE));
					}
					$feuser->set($field->getFieldName(), $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE));
				}
			} else {
				if ($this->_configuration['global.']['useFieldAsUsername'] && $this->_configuration['global.']['useFieldAsUsername'] === $field->getFieldName()) {
					$feuser->set('username', $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE));
				}
				$feuser->set($field->getFieldName(), $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE));
			}
		}
		$this->_controller->notifyObservers('onEditBeforeSave', array('feuser' => &$feuser));
		if (count($confirmValues) > 0) {
			$confirmValuesDb = serialize($confirmValues);
			$feuser->set('tx_feuserregister_temporarydata', $confirmValuesDb);
			if (strlen(trim($this->_configuration['global.']['confirmationOnUpdateFields'])) > 0 || $this->_configuration['global.']['userEmail.']['onUpdate']) {
				$controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');
				$marker['###CONFIRMATION_URL###'] = t3lib_div::makeRedirectUrl(
					str_replace('tx_feuserregister_controller_UserRegistration', 'tx_feuserregister',
						t3lib_div::getIndpEnv('TYPO3_SITE_URL').$controller->pi_linkTP_keepPIvars_url(array(
							'confirmationCode'	=> md5($feuser->uid . $feuser->crdate . $confirmValuesDb . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']),
							'cmd'				=> 'confirm'
						), 0, 1, $this->_configuration['pages.']['confirm'])
					)
				);
			}
		}	
		if ($this->_configuration['global.']['userGroupsAfterUpdate']) {
			$feuser->set('usergroup', $this->_configuration['global.']['userGroupsAfterRegistration']);
		}
		if ($feuser->save()) {
			$this->clearData();
			tx_feuserregister_SessionRegistry::set('tx_feuserregister_reloadhash', $reloadHash);
			if ($this->_configuration['global.']['adminEmail.']['onUpdate']) {
				tx_feuserregister_Mailer::send('admin', 'onupdate', $marker);
			}
			if (strlen(trim($this->_configuration['global.']['confirmationOnUpdateFields'])) > 0 || $this->_configuration['global.']['userEmail.']['onUpdate']) {
				tx_feuserregister_Mailer::send('user', 'onupdate', $marker);
			}
		} else {
			$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_Database');
			throw new $exceptionClass('error while updae fe user', 1200);
		}
		tx_feuserregister_Registry::set('tx_feuserregister_feuser', $feuser);
		$this->_controller->notifyObservers('onEditAfterSave', array('feuser' => &$feuser));
		return t3lib_parsehtml::substituteMarkerArray($this->_templateContent, $marker, '', 0, 1);
	}

	protected function _processRegister($allFields, $marker, $reloadHash) {
		/* @var $feuser tx_feuserregister_model_FeUser */
		$feuser = t3lib_div::makeInstance('tx_feuserregister_model_FeUser');
		$feuser->set('pid', $this->_configuration['pages.']['storagePid'])
			->set('usergroup', $this->_configuration['global.']['userGroupsAfterRegistration'])
			->set('disable', 1)
			->save();

		foreach ($allFields as $field) {
			if ($this->_configuration['global.']['useFieldAsUsername'] && $this->_configuration['global.']['useFieldAsUsername'] === $field->getFieldName()) {
				$feuser->set('username', $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE));
			}
			$feuser->set($field->getFieldName(), $field->getValue(tx_feuserregister_model_Field::PARSE_DATEBASE));
		}
		
		$this->_controller->notifyObservers('onRegisterBeforeSave', array('feuser' => &$feuser, 'allFields' => $allFields));
			
		if ($this->_configuration['global.']['emailConfirmation'] || $this->_configuration['global.']['userEmail.']['onRegister']) {
			$controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');
			$marker['###CONFIRMATION_URL###'] = t3lib_div::makeRedirectUrl(
				str_replace('tx_feuserregister_controller_UserRegistration', 'tx_feuserregister',
					t3lib_div::getIndpEnv('TYPO3_SITE_URL').$controller->pi_linkTP_keepPIvars_url(array(
						'confirmationCode'	=> md5($feuser->uid . $feuser->crdate . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']),
						'cmd'				=> 'confirm'
					), 0, 1, $this->_configuration['pages.']['confirm'])
				)
			);
		} else {
			$feuser->set('disable', 0);
		}
		if ($feuser->save()) {
			$this->clearData();
			tx_feuserregister_SessionRegistry::set('tx_feuserregister_reloadhash', $reloadHash);
			if ($this->_configuration['global.']['emailConfirmation'] || $this->_configuration['global.']['userEmail.']['onRegister']) {
				tx_feuserregister_Mailer::send('user', 'onregister', $marker);
			}
			if ($this->_configuration['global.']['adminEmail.']['onRegister']) {
				tx_feuserregister_Mailer::send('admin', 'onregister', $marker);
			}
		} else {
			$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_Database');
			throw new $exceptionClass('error while creating fe user', 1100);
		}
		tx_feuserregister_Registry::set('tx_feuserregister_feuser', $feuser);
		$this->_controller->notifyObservers('onRegisterAfterSave', array('feuser' => &$feuser, 'allFields' => $allFields));
		return t3lib_parsehtml::substituteMarkerArray($this->_templateContent, $marker, '', 0, 1);
	}

	public function setSteps(array $steps) {
		array_shift($steps);
		$this->_steps = $steps;
	}

	protected function _getFieldMarker() {
		$marker = array();
		foreach ($this->_steps as $step) {
			$fields = $step->getFields();
			foreach ($fields as $field) {
				$marker['###FIELD_'.$field->getFieldName().'###'] = $field->getValue(tx_feuserregister_model_Field::PARSE_HTML);
			}
		}
		return $marker;
	}

	protected function _getLabelmarker() {
		$marker = array();
		foreach ($this->_steps as $step) {
			$fields = $step->getFields();
			foreach ($fields as $field) {
				$marker['###LABEL_'.$field->getFieldName().'###'] = $field->getLabel();
			}
		}
		return $marker;
	}
	
	protected function _getGlobalMarker() {
		$marker = array(
			'###FORM_URL###' 		=> $this->_controller->cObj->typoLink_URL(array('parameter' => $GLOBALS['TSFE']->id)),	
			'###HIDDEN_FIELDS###'		=> $this->_getHiddenFields()
		);
		return $marker;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_success.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_success.php']);
}

?>
