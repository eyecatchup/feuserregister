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

class tx_feuserregister_model_StepManager {
	protected $_steps = array(null);
	protected $_currentStep = null;
	protected $_stepCounter = 0;
	protected $_request = null;
	protected $_configuration = null;
	
	public function __construct() {
		$mode 					= tx_feuserregister_Registry::get('tx_feuserregister_mode');
		$this->_configuration	= tx_feuserregister_Registry::get('tx_feuserregister_configuration');
		$stepConfigurations		= $this->_configuration[$mode.'.'];
		$this->_stepCounter		= count($stepConfigurations);
		$this->_request			= t3lib_div::makeInstance('tx_feuserregister_Request');
		$firstStep				= null;
		
		foreach ($stepConfigurations as $stepName => $stepConfiguration) {
			$stepName = substr($stepName, 0, -1);
			if ($firstStep === null) {
				$firstStep = $stepName;
			}
			switch ($stepName) {
				case 'preview':
					$className = t3lib_div::makeInstanceClassName('tx_feuserregister_model_Preview');
					$this->_steps[$stepName] = new $className($stepName, $stepConfiguration, $mode);
					$this->_steps[$stepName]->setSteps($this->_steps);
				break;
				case 'success':
					$className = t3lib_div::makeInstanceClassName('tx_feuserregister_model_Success');
					$this->_steps[$stepName] = new $className($stepName, $stepConfiguration, $mode);
					$this->_steps[$stepName]->setSteps($this->_steps);
				break;
				default:
					$className = t3lib_div::makeInstanceClassName('tx_feuserregister_model_Step');
					$this->_steps[$stepName] = new $className($stepName, $stepConfiguration, $mode);
				break;
			}
		}
		if (strlen($this->_request->get('step'))) {
			$this->_currentStep = $this->_request->get('step');
		} else {
			$this->_currentStep = $firstStep;
		}
	}
	
	/**
	 * Gets the current step.
	 *
	 * @return tx_feuserregister_model_Step
	 */
	public function getCurrentStep() {
		return $this->_steps[$this->_currentStep]; 
	}
	
	/**
	 * Gets the next step.
	 *
	 * @return tx_feuserregister_model_Step
	 */
	public function getNextStep() {
		$found = false;
		foreach ($this->_steps as $stepName => $step) {
			if ($found) {
				return $step;
			}
			if ($this->_currentStep === $stepName) {
				$found = true;
			}
		}
	}
	
	/**
	 * Gets the previous step.
	 *
	 * @return tx_feuserregister_model_Step
	 */
	public function getPreviousStep() {
		$lastStep = null;
		foreach ($this->_steps as $stepName => $step) {
			if ($this->_currentStep == $stepName) {
				return $lastStep;
			}
			$lastStep = $step;
		}
	}
	
	public function getStep($stepName) {
		return $this->_steps[$stepName];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_stepmanager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_stepmanager.php']);
}

?>
