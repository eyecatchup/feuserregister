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
require_once(PATH_feuserregister . 'interfaces/interface.tx_feuserregister_interface_command.php');

class tx_feuserregister_command_Register implements tx_feuserregister_interface_Command {
	const MODE = 'register';
	
	protected $_controller = null;
	protected $_request = null;

	public function __construct() {
		$this->_request = t3lib_div::GParrayMerged('tx_feuserregister');
		$this->_controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');
		tx_feuserregister_Registry::set('tx_feuserregister_mode', self::MODE);
	}
	
	/**
	 * @see tx_feuserregister_interface_Command::execute()
	 *
	 */
	public function execute() {
		$this->_controller->notifyObservers('onRegisterStart');
		$stepManager = t3lib_div::makeInstance('tx_feuserregister_model_StepManager');
		$currentStep = $stepManager->getCurrentStep();
		switch ($this->_request['action']) {
			case 'previousStep':
				$this->_controller->notifyObservers('onEnterPreviousStep');
				if ($currentStep->isValid()) {
					$currentStep->storeData();
					$currentStep = $stepManager->getPreviousStep();
					if ($currentStep === null) {
						$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_StepManager');
						throw new $exceptionClass('previous step not available', 3100);
					} else {
						$currentStep->setValidate(false);
					}
				}
			break;
			case 'getStep':
				$this->_controller->notifyObservers('onEnterGetStep');
				if ($currentStep->isValid()) {
					$currentStep->storeData();
					$currentStep = $stepManager->getStepByNumber($this->_request['step']);
					if ($currentStep === null) {
						$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_StepManager');
						throw new $exceptionClass('step '.$this->_request['step'].' not available', 3200);
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
		$this->_controller->notifyObservers('onRegisterEnd');
		return $currentStep->render();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/commands/class.tx_feuserregister_command_register.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/commands/class.tx_feuserregister_command_register.php']);
}

?>