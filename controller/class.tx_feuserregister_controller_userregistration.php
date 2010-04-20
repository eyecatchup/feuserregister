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

define('PATH_feuserregister', t3lib_extMgm::extPath('feuserregister'));
require_once(PATH_tslib.'class.tslib_pibase.php');

require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_commandresolver.php');
require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_localizationmanager.php');
require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_mailer.php');
require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_validatorfactory.php');
require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_registry.php');
require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_request.php');
require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_sessionregistry.php');
require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_tcafieldfactory.php');
require_once(PATH_feuserregister . 'classes/class.tx_feuserregister_transformerfactory.php');

require_once(PATH_feuserregister . 'exceptions/class.tx_feuserregister_exception_confirm.php');
require_once(PATH_feuserregister . 'exceptions/class.tx_feuserregister_exception_field.php');
require_once(PATH_feuserregister . 'exceptions/class.tx_feuserregister_exception_database.php');
require_once(PATH_feuserregister . 'exceptions/class.tx_feuserregister_exception_stepmanager.php');
require_once(PATH_feuserregister . 'exceptions/class.tx_feuserregister_exception_tca.php');
require_once(PATH_feuserregister . 'exceptions/class.tx_feuserregister_exception_transformer.php');

require_once(PATH_feuserregister . 'interfaces/interface.tx_feuserregister_interface_observable.php');

require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_feuser.php');
require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_field.php');
require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_preview.php');
require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_sessionuser.php');
require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_success.php');
require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_step.php');
require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_stepmanager.php');

require_once(PATH_feuserregister . 'view/class.tx_feuserregister_view_error.php');

/**
 * central application controller for the feuserregister extension, this is the "plugin"
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage feuserregister
 */
class tx_feuserregister_controller_UserRegistration extends tslib_pibase implements tx_feuserregister_interface_Observable  {
	protected $_observers = array();
	
	public $prefixId      = 'tx_feuserregister_controller_UserRegistration';		// Same as class name
	public $scriptRelPath = 'controller/class.tx_feuserregister_controller_userregistration.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'feuserregister';	// The extension key.

	public $configuration;
	public $flexform;
	
	/**
	 * constructor for class tx_feuserregister_controller_UserRegistration
	 */
	public function __construct() {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_feuserregister']['addObserver'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_feuserregister']['addObserver'] as $classReference) {
				$observer = & t3lib_div::getUserObj($classReference);
				if ($observer instanceof tx_feuserregister_interface_Observer) {
					$this->attachObserver($observer);
				}
			}
		}
	}

	public function initialize($configuration) {
		$this->notifyObservers('onInitStart', array('configuration' => &$configuration));
		$this->configuration = t3lib_div::array_merge_recursive_overrule(
			$configuration,
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']
		);
			// we need this public $conf array for the locallang functions
		$this->conf = $this->configuration;
		
		$this->tslib_pibase();
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj = 1; // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();
		$this->pi_loadLL();
		
		
		$piFlexForm = $this->cObj->data['pi_flexform'];
		foreach ( $piFlexForm['data'] as $sheet => $data ) {
			foreach ( $data as $lang => $value ) {
				foreach ( $value as $key => $val ) {
					$this->flexform[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
				}
			}
		}

		tx_feuserregister_Registry::set('tx_feuserregister_configuration', $this->configuration);
		tx_feuserregister_Registry::set('tx_feuserregister_flexform', $this->flexform);
		tx_feuserregister_Registry::set('tx_feuserregister_controller', $this);
		$this->notifyObservers('onInitEnd');
	}

	public function execute($content, $configuration) {
		try {
			$this->initialize($configuration);

			/* @var $commandResolver tx_feuserregister_CommandResolver */
			$commandResolver = t3lib_div::makeInstance('tx_feuserregister_CommandResolver');
			$command = $commandResolver->getCommand();

			$content = $command->execute();			
			return $this->pi_wrapInBaseClass($content);
		
		} catch (tx_feuserregister_exception_Confirm $exception) {
			$viewName = t3lib_div::makeInstanceClassName('tx_feuserregister_view_Error');
			$model = $exception;
		} catch (tx_feuserregister_exception_Field $exception) {
			$viewName = t3lib_div::makeInstanceClassName('tx_feuserregister_view_Error');
			$model = $exception;
		} catch (tx_feuserregister_exception_Database $exception) {
			$viewName = t3lib_div::makeInstanceClassName('tx_feuserregister_view_Error');
			$model = $exception;
		} catch (tx_feuserregister_exception_StepManager $exception) {
			$viewName = t3lib_div::makeInstanceClassName('tx_feuserregister_view_Error');
			$model = $exception;
		} catch (tx_feuserregister_exception_Tca $exception) {
			$viewName = t3lib_div::makeInstanceClassName('tx_feuserregister_view_Error');
			$model = $exception;
		} catch (tx_feuserregister_exception_Transformer $exception) {
			$viewName = t3lib_div::makeInstanceClassName('tx_feuserregister_view_Error');
			$model = $exception;
		} catch (Exception $exception) {
			die ('unhandled exception: ' . $exception);
		}
		$view = new $viewName();
		$view->setModel($model);
		return $view->render();
	}
	
	/**
	 * @see tx_feuserregister_interface_Observable::attachObserver()
	 *
	 * @param tx_feuserregister_interface_Observer $observer
	 */
	public function attachObserver(tx_feuserregister_interface_Observer $observer) {
		$this->_observers[] = $observer;
	}
	
	/**
	 * @see tx_feuserregister_interface_Observable::detachObserver()
	 *
	 * @param tx_feuserregister_interface_Observer $observer
	 */
	public function detachObserver(tx_feuserregister_interface_Observer $observer) {
		$this->_observers = array_diff($this->_observers, array($observer));
	}
	
	/**
	 * @see tx_feuserregister_interface_Observable::notifyObservers()
	 *
	 * @param string $event Name of the event
	 * @param array $params Parameters to be forwarded to the observer
	 * @return boolean Whether to cancel further processing
	 */
	public function notifyObservers($event, array $params = array()) {
		$cancel = FALSE;

		foreach ($this->_observers as $observer) {
			$result = (bool) $observer->update($event, $params, $this);
			$cancel = ($cancel || $result);
		}

		return $cancel;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/controller/class.tx_feuserregister_controller_userregistration.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/controller/class.tx_feuserregister_controller_userregistration.php']);
}

?>
