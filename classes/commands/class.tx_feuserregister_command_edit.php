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

class tx_feuserregister_command_Edit implements tx_feuserregister_interface_Command {
	const MODE = 'edit';
	
	protected $_controller = null;
	protected $_request = null;

	public function __construct() {
		$this->_request = t3lib_div::makeInstance('tx_feuserregister_Request');
		$this->_controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');
		$feuser = tx_feuserregister_SessionRegistry::get('tx_feuserregister_feuser');
		if (!$feuser instanceof tx_feuserregister_model_FeUser) {
			$feuser = t3lib_div::makeInstance('tx_feuserregister_model_FeUser', $GLOBALS['TSFE']->fe_user->user['uid']);
			tx_feuserregister_SessionRegistry::set('tx_feuserregister_feuser', $feuser);
		}
		$currentFeuser = tx_feuserregister_SessionRegistry::get('tx_feuserregister_currentFeuser');
		if (!$currentFeuser instanceof tx_feuserregister_model_FeUser) {
			$currentFeuser = t3lib_div::makeInstance('tx_feuserregister_model_FeUser', $GLOBALS['TSFE']->fe_user->user['uid']);
			tx_feuserregister_SessionRegistry::set('tx_feuserregister_currentFeuser', $currentFeuser);
			$sessionUser = t3lib_div::makeInstance('tx_feuserregister_model_SessionUser');
			$attributes = $currentFeuser->getAttributes();
			foreach ($attributes as $attribute) {
				$sessionUser->set($attribute, $currentFeuser->get($attribute));
			}
			$sessionUser->storeData();
		}

		tx_feuserregister_Registry::set('tx_feuserregister_mode', self::MODE);
	}
	
	/**
	 * @see tx_feuserregister_interface_Command::execute()
	 *
	 */
	public function execute() {
		$this->_controller->notifyObservers('onEditStart');
		/* @var $stepManager tx_feuserregister_model_StepManager */
		$stepManager = t3lib_div::makeInstance('tx_feuserregister_model_StepManager');
		$currentStep = $stepManager->getCurrentStep();
		switch ($this->_request->get('action')) {
			case 'previousStep':
				$this->_controller->notifyObservers('onEnterPreviousStep');
				if ($currentStep->isValid()) {
					$currentStep->storeData();
					$currentStep = $stepManager->getPreviousStep();
					if ($currentStep === null) {
						$exception = t3lib_div::makeInstance(
							'tx_feuserregister_exception_StepManager',
							'previous step not available',
							3100
						);
						throw $exception;
					} else {
						$currentStep->setValidate(false);
					}
				}
			break;
			case 'getStep':
				$this->_controller->notifyObservers('onEnterGetStep');
				if ($currentStep->isValid()) {
					$currentStep->storeData();
					$currentStep = $stepManager->getStepByNumber($this->_request->get('step'));
					if ($currentStep === null) {
						$exception = t3lib_div::makeInstance(
							'tx_feuserregister_exception_StepManager',
							'step ' . $this->_request->get('step') . ' not available',
							3200
						);
						throw $exception;
					} else {
						$currentStep->setValidate(false);
					}
				}
			break;
			case 'nextStep':
				$this->_controller->notifyObservers('onEnterNextStep');
				if ($currentStep->isValid()) {
					$currentStep->storeData();
					$currentStep = $stepManager->getNextStep();
					$currentStep->setValidate(false);
				}
			break;
			default:
			break;
		}
		$this->_controller->notifyObservers('onEditEnd');
		return $currentStep->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/commands/class.tx_feuserregister_command_edit.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/commands/class.tx_feuserregister_command_edit.php']);
}

?>