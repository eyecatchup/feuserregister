<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Cross Content Media / e-netconsulting <dev@cross-content.com / team@e-netconsulting.de>
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
 * Class that implements the controller "frontenduserregistration" for tx_feuserregister.
 *
 * switched controller with swiches to switch between create /
 * edit / preview mode
 * 
 *
 * @author	Cross Content Media / e-netconsulting KG <dev@cross-content.com / team@e-netconsulting.de>
 * @package	TYPO3
 * @subpackage	tx_feuserregister
 */

tx_div::load('tx_lib_controller');
tx_div::autoLoadAll('fn_lib');
#tx_div::load('tx_fnlib_base');

class tx_feuserregister_controller_frontenduserregistration extends tx_lib_controller {

	var $targetControllers = array();
	var $functionsObject;
	var $currentStep = FALSE;
	var $stepKey;
	var $steps = array();
	var $origKeysMapping = array();
	var $translator;
	var $cObjectWrapper;
	var $feuserFolderUid;
	var $newUserUid;
	var $extjsIncluded;
	var $showPreview;
	var $validationsPassed = TRUE;
	var $currentStepParam = FALSE;
	var $stepsConfigured = FALSE;
	var $validationMethods = array();
	var $jsRequiredArrayCounter = 0;
	var $errors = array();
	var $stepRequirementErrors = array();
	var $model;
	var $additionalMarker = array();
	var $doubleoptin_code_user = FALSE;
	var $doubleoptin_code_admin = FALSE;
	var $doubleoptin_col_user = 'doubleoptin_code_user';
	var $doubleoptin_col_admin = 'doubleoptin_code_admin';
	var $doubleoptin_confirmed_col_user = 'doubleoptin_confirmed_user';
	var $doubleoptin_confirmed_col_admin = 'doubleoptin_confirmed_admin';
	var $hookHandler;
	var $belowForm;
	var $userNameInsertedThisRun = FALSE;
	var $isEditMode = FALSE;
	var $fieldsToRemove = array();
	var $additionalFields = array(
	     'fe_users' => array()
      );
    var $session_vars = array();
    var $isAfterPreview = FALSE;
	var $randPassword; 
	var $backButtonPressed = FALSE;
    var $defaultAction;
    var $submittedActionEqualsRunAction = FALSE;
    
    function tx_feuserregister_controller_frontenduserregistration($parameter1 = null, $parameter2 = null) {
    	parent::tx_lib_controller($parameter1, $parameter2);
    	
    	if (isset($_GET['feuserregister']['action'])){
    		if ('confirm' == $_GET['feuserregister']['action']){
    			# $this->configurations->set('defaultAction', 'confirm');
    			$this->action = 'confirm';
    		}
    	}
#    	debug($_GET);
        $this->setDefaultDesignator('feuserregister');
        $this->initHookhandler();
        $this->hookHandler->call('init', $this);
    }
    
    function doPreActionProcessings(){
    	if ($GLOBALS['runalready']) {
#    		debug($_POST);
    		unset($_GET['feuserregister']['action']);
    		unset($_POST['feuserregister']['action']);
#    		debug($_POST);
		$configurations = $this->configurations->getHashArray();
		// 1. + 2.) A defaultAction can be set as class property or by TS.
		$this->action = $configurations['defaultAction'] ? $configurations['defaultAction']   : $this->defaultAction;
		$this->defaultAction = $configurations['defaultAction'];
		
    	$this->action .= 'Action';	
#    		debug($this->action);
#    		debug('runalready');
    	}
#    	debug('1');
		
        if ($this->parameters->get('feuserregister_submitbutton'.$this->action)) {
        	$this->submittedActionEqualsRunAction = TRUE;
        }
		$GLOBALS['runalready'] = 1;
    	$this->isAfterPreview = $this->parameters->get('returnFromPreview');
	    if ($this->isAfterPreview) $this->isAfterPreview = TRUE; // von 1 auf TRUE f�r ===
		$this->randPassword = rand(100000,999999);
	    #	    debug($this->isAfterPreview);
    }
    
    function isAfterPreview() {
    	return $this->isAfterPreview;
    }

    function initHookhandler(){
    	$fnlibbaseClassName = tx_div::makeInstanceClassName('tx_fnlib_base');
    	$fnlibbase = new $fnlibbaseClassName($this);
    	$this->hookHandler = $fnlibbase->getHookHandler($this->getDefaultDesignator());
    	$this->hookHandler->init();	
    	
    }
    
    function getBelowForm(){
    	return $this->belowForm;
    }
    
    function getSteps(){
    	if (!$this->steps){
        	$steps = $this->configurations->get('config.steps.');
        	$this->steps = $steps;
//	        debug('steps');
//	        debug($this->steps);
        	$tmp = $this->parameters->get('step');
    		if (!$tmp){
    			$this->currentStep = 0;
    		}
    	}
    	#$tmp = $this->_getStepKey();
    	return $this->steps;
    }
    
    function getAdditionalMarker() {
    	$additionalMarkerFromTS = $this->configurations->get('config.additionalMarker.');
    	if (is_array($additionalMarkerFromTS)) {
    		foreach ($additionalMarkerFromTS as $key => $value) {
				$this->additionalMarker[$key] = $value;
    		}
    	}
    	#t3lib_div::debug($this->additionalMarker);
    	return $this->additionalMarker;
    }
    
    function getFieldByName($name) { 		
 		$this->model->rewind();
 		$feuserobj = $this->model->current();
 		return $feuserobj->get($name);
 	}
    
    
    function setDoubleOptInCode($address, $doubleoptin_code) {
    	if ($address == 'user') {    	
    		$this->doubleoptin_code_user = $doubleoptin_code;
    	} else if ($address == 'admin') {
    		$this->doubleoptin_code_admin = $doubleoptin_code;
    	}
    }
    
//    function getConfirm
    
    function getFeUserUID(){
    	return $GLOBALS['TSFE']->fe_user->user['uid'];
    }
    
    function getConfirmCode($address) {
    	if ($address == 'user' || (int)$address == 2) {    	
    		return $this->doubleoptin_col_user;
    	} else if ($address == 'admin' || (int)$address == 1) {
    		return $this->doubleoptin_col_admin;
    	} 
    	return false;
    }
    
    function getConfirmLinkHrefByUsername($username){
    	$doubleoptin_code = $this->model->getUserConfirmationCode($username);
      	$designator = $this->getDefaultDesignator();
    	
    
      if (!$doubleoptin_code) return FALSE;
    	$id = $GLOBALS['TSFE']->id;
    	$action = 'confirm';
    	$params = array (
    		'parameter' => $id
    	);
    	$urlParameters = array (
    			'action' => $action,
    			'confirmid' => $doubleoptin_code,
    			'confirmmode' => 'user',
    	); 
		$linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
     	$linkObj = new $linkObjClassname();
      	$linkObj->parameters($urlParameters);
	  	$linkObj->idAttribute($id);  
  		$linkObj->destination($this->getDestination());
  		$linkObj->designator($this->getDesignator());
	  	$link = $linkObj->makeUrl(); 
    	#$link = 'index.php?'.$designator.'[action]='.$action.'&id='.$id.'&'.$designator.'[confirmid]='.$doubleoptin_code.'&'.$designator.'[confirmmode]='.$address;
    	
	    if($GLOBALS['TSFE']->config['config']['baseURL']) {
			$baseUrl = $GLOBALS['TSFE']->config['config']['baseURL'];
			if(substr($baseUrl,-1,1)!='/') $baseUrl = $baseUrl.'/';
			$result = $baseUrl.$link;
		} else {
			$result = 'http://'.t3lib_div::getIndpEnv("HTTP_HOST").'/'.$link;
		}

		return $result;	
    }

    
    function getConfirmLinkHref($address){
      $username = $this->parameters->get('username');
    	if ($address == 'user' || (int)$address == 2) {    	
    		$doubleoptin_code = $this->model->getUserConfirmationCode($username);
    	} else if ($address == 'admin' || (int)$address == 1) {
    		$doubleoptin_code = $this->model->getAdminConfirmationCode($username);
    	} 
      $designator = $this->getDefaultDesignator();
    	
    	if (!$doubleoptin_code) return FALSE;
    	$id = $GLOBALS['TSFE']->id;
    	$action = 'confirm';
    	$params = array (
    		'parameter' => $id
    	);
    	$urlParameters = array (
    			'action' => $action,
    			'confirmid' => $doubleoptin_code,
    			'confirmmode' => $address,
    	); 
		$linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
     	$linkObj = new $linkObjClassname();
      	$linkObj->parameters($urlParameters);
	  	$linkObj->idAttribute($id);  
  		$linkObj->destination($this->getDestination());
  		$linkObj->designator($this->getDesignator());
	  	$link = $linkObj->makeUrl();    	
    	#$link = 'index.php?'.$designator.'[action]='.$action.'&id='.$id.'&'.$designator.'[confirmid]='.$doubleoptin_code.'&'.$designator.'[confirmmode]='.$address;
    	
	    if($GLOBALS['TSFE']->config['config']['baseURL']) {
			$baseUrl = $GLOBALS['TSFE']->config['config']['baseURL'];
			if(substr($baseUrl,-1,1)!='/') $baseUrl = $baseUrl.'/';
			$result = $baseUrl.$link;
		} else {
			$result = 'http://'.t3lib_div::getIndpEnv("HTTP_HOST").'/'.$link;
		}
		return $result;	
    }
    
    function setAdditionalMarker($key, $value) {
    	$this->additionalMarker[$key] = $value;
    }
    
    function setNewUserUID($newuseruid){
    	$this->newUserUid = $newuseruid;
    	$this->userNameInsertedThisRun = TRUE;
    }
    
    function getNewUserUID(){
    	return $this->newUserUid;
    }
    
    function getfeuserFolderUid(){
    	if (!$this->feuserFolderUid){
    		$this->feuserFolderUid = $this->configurations->get('config.pid');
    	}
    	return $this->feuserFolderUid;
    }
    
    function backButtonIsPressed($step) {    	
    	if (($this->parameters->get('backbutton'.$this->action)) || $this->parameters->get('backbutton')) {
    		$this->backButtonPressed = TRUE;
    		return TRUE;
    	}
    	return FALSE;	
    }
    
    function initStepConfig(){
    	if (!$this->stepsConfigured) {
#    		debug($this->configurations->_iterator);
    		$this->steps = $this->configurations->get('config.steps.');
#    		debug($this->steps);
    		//    		debug('steps2');
//    		debug($GLOBALS['TSFE']->setup);
    		$tmp = $this->parameters->get('step');
    		$this->currentStepParam = $tmp;
    		if (!$tmp) {
    			$this->currentStep = 0;
    		}
    		
    		if ($this->backButtonIsPressed($tmp) && $tmp && !$this->_stepRequirementsFulfilled()) {
#    			debug('step back');
    			$tmp--;
    		}
#    		debug($tmp);
    		$this->currentStep = $tmp;
    		$this->stepKey = $this->_getStepKey();
    		
    		$this->stepsConfigured = TRUE;
    	}
//    	t3lib_div::debug('INIT');
//    	t3lib_div::debug($this->stepKey);
//    	t3lib_div::debug($this->currentStep);
//    	t3lib_div::debug($this->origKeysMapping);
//    	t3lib_div::debug($this->steps);
//    	t3lib_div::debug('ENDINIT');
    	
    }
    
    function getCurrentStep(){
    	if ($this->currentStep === FALSE){
    		$step = $this->parameters->get('step');
    		$this->currentStep = $step;
    		if (!$step){
    			$this->currentStep = 0;
    		}
    		
    		$i = 0;
	    	foreach ($steps as $stepkey => $valuearray){
    			if ($step == $i) {
    				$this->stepKey = $stepkey;
    			}
    			$this->origKeysMapping[$i] = $stepkey;
    			$i++;
    		}
    	}
    	return $this->currentStep;
    }
    
    function _getStepKey(){
#    	debug($this->action);
    	if (!$this->stepKey){
    		$tmpstep = $this->parameters->get('step');
    		if (!$tmpstep){
    			$tmpstep = 0;
    		}
    		$steps2 = array();
    		$i = 0;
    		
#    		$firstStepKeyArray = array_keys($this->steps);
#    		$firstStepKey = $firstStepKeyArray[0];
#    		$this->stepKey = $firstStepKey;
#    		$this->currentStep = 0;
#debug('steps start');
#    		debug($this->steps);
#debug('steps fin');
	    	foreach ($this->steps as $stepkey => $valuearray){
    			if ($tmpstep == $i) {
    				$this->stepKey = $stepkey;
    				$this->currentStep = $i;
    			}
    			$this->origKeysMapping[$i] = $stepkey;
    			$steps2[$i] = $valuearray;
    			$i++;
    		}
    		$this->steps = $steps2;
    		#t3lib_div::debug($this->origKeysMapping);
    	}
    	
    	return $this->stepKey;
    }
    
    function isFirstStep(){
    	$stepParam = $this->parameters->offsetExists('step');
    	if (!$stepParam) {
    		#t3lib_div::debug('isFirstStep === TRUE');
    		return TRUE;
    	}
    	if ($stepParam == 1 && $this->parameters->get('step') == 0 && $this->backButtonPressed) {
    		//unschoen, aber das wird ben�tigt um bei "back from preview" das gleiche verhalten zu erzielen wie beim ersten aufruf der seite
    		return TRUE;
    	}
    	return FALSE;
    }
    
    function getNextStep()
    {
    	$nextStep = (((int)$this->currentStep) + 1);
    	if ($this->isFirstStep()) $nextStep = 0;
    	if ($this->validationsPassed && $this->_stepRequirementsFulfilled()) {
    		return $nextStep;    		
    	}
    	return $this->currentStep;
    }
    
    /**
     * Implementation of isPreview()
     *
     * alias of showPreview()
     * @return boolean
     */
    function isPreview()
    {
    	return $this->showPreview();
    }
    
    /**
     * Implementation of the chjeck wether the user want to return to the previous step
     *
     * @return booelan
     */
    function stepBackButtonPressed() {
    	if ($this->backButtonPressed) return TRUE;
    	return false;
    }
    
    
    function showPreview(){
    	if ($this->showPreview === TRUE || $this->showPreview === FALSE) return $this->showPreview;
    	$this->showPreview = FALSE;
    	$steps = $this->getSteps();
    	
    	$origKey = $this->origKeysMapping[$this->currentStep];
    	#t3lib_div::debug($origKey);
#    	t3lib_div::debug($this->configurations->get('config.steps.'.$origKey));
    	if ($this->configurations->get('config.steps.'.$origKey.'preview') == 1){
    		// preview param set === the last step was a preview
    		if ($this->parameters->get('preview') == 0 || ($this->parameters->get('preview') == 1 && ($this->stepBackButtonPressed())) || $this->isFirstStep()) {
    			#t3lib_div::debug('showPreview == FALSE');
    			$this->showPreview = FALSE;
    			return FALSE;
    		}
    		$this->parameters->offsetUnset('preview');
    		$this->showPreview = TRUE;
    		return TRUE;
    	}
    	return FALSE;
    }
    
    function exists_username() {
      if (!$this->submittedActionEqualsRunAction) return FALSE;
      if ($this->userNameInsertedThisRun) return FALSE;
    	$username = $this->parameters->get('username');
    	if ($username && !empty($username) && $this->model->existsUsername($username)) {
  			$newuid_exists = $GLOBALS['TSFE']->fe_user->getKey("ses","newuserid");
  			if ($newuid_exists) {
  				$this->setError('user_aleady_exists_reload_error', '%%%USER_ALREADY_EXISTS_RELOAD_ERROR%%%');
  			}
    		return TRUE;
    	}
    	return FALSE;
    }
    
    function _stepRequirementsFulfilled(){
    	//FIXME: need check if the stepcounter can set to next step
    	if (!$this->isEditMode() && $this->exists_username()) {
    		#t3lib_div::debug('ret FALSE USERNAME EXISTS');
    		$this->stepRequirementErrors['username'] = 'username_exist';
#    		t3lib_div::debug($this->parameters);
    		$this->parameters->set('preview', 0);
#    		t3lib_div::debug($this->parameters);
    		return FALSE;
    	}
    	if ($this->showPreview()) {
    		#t3lib_div::debug('ret FALSE');
    		return FALSE;
    	}
    	return true;
    }
    
    function isSaveStep(){
    	if (!$this->validationsPassed || $this->backButtonPressed) return FALSE;
    	if ($this->showPreview()) {
    		#t3lib_div::debug('ret FALSE is Save (prev)');
    		return FALSE;
    	}
    	$current = $this->getCurrentStep();
    	$steps = $this->getSteps();
    	$savekey = ((int)end(array_keys($steps)));
#    	t3lib_div::debug($steps);
#    	t3lib_div::debug($savekey);
#    	t3lib_div::debug($current);
    	if ($current == $savekey && !$this->isFirstStep()) {
    		#t3lib_div::debug('ret TRUE is Save');
    		return true;
    	}
    	#t3lib_div::debug('ret FALSE is Save');
    	return false;    	    	
    }
    
    function sendMail($mailConfig){
//        debug($mailConfig);
        $htmlmailClassName = tx_div::makeInstanceClassName('t3lib_htmlmail');
        $this->htmlMail = t3lib_div::makeInstance('t3lib_htmlmail');
		$this->htmlMail->start();
		
		$this->htmlMail->recipient = $mailConfig['recipient'];
		$this->htmlMail->recipient_copy = $mailConfig['recipient_copy'];
		$this->htmlMail->replyto_email = $mailConfig['replyto_email'];
		$this->htmlMail->replyto_name = $mailConfig['replyto_name'];
		$this->htmlMail->subject = $mailConfig['subject'];
		$this->htmlMail->from_email = $mailConfig['from_email'];
		$this->htmlMail->from_name = $mailConfig['from_name'];
		$this->htmlMail->returnPath = $mailConfig['returnPath'];
		$this->htmlMail->addPlain($mailConfig['plainText']);
		$this->htmlMail->setHTML($this->htmlMail->encodeMsg($mailConfig['htmlText']));
		$this->hookHandler->call('beforeSendMail', $this, $mailConfig);
//		debug('send email to: ' . $mailConfig['recipient']);
		$this->htmlMail->send($mailConfig['recipient']);    
//		debug('mail sent2');
		$this->hookHandler->call('afterSendMail', $this, $mailConfig);
    }
    
    
    function handleNextActionSetting(){
    	// FIXME: wich is the next action ?
//    	configurations --> save step abgehandelt?
//		
    }
    
	function _removeDot($var)
	{
	   return substr($var, 0, strlen($var)-1);
	}
        
    function confirmAction(){
    	$modelClassName = tx_div::makeInstanceClassName('tx_feuserregister_model_frontenduserregistration');
      $viewClassName = tx_div::makeInstanceClassName('tx_feuserregister_view_feuserregister');
      $entryClassName = tx_div::makeInstanceClassName($this->configurations->get('config.entryClassName'));

      $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
  			
      $view = new $viewClassName($this);
      
      #$this->parameters->offsetUnset('action');
      $model = new $modelClassName($this);
      
      $confirmMode = $this->parameters->get('confirmmode');
      $confirmID = $this->parameters->get('confirmid');
      if (!$confirmID || !$confirmMode) {
      	die('No hacking!');
      }
      $confirmColumn = $this->getConfirmCode($confirmMode);
      
      $out = $this->confirm($confirmColumn, $confirmID);
      return $out;
    }
    
    
    function fetchMailconfig($configpath, $model = ''){
    	$allowedMailConfigVars = array (
	       	'recipient', 
	        'recipient_copy', 
	        'replyto_email', 
	        'replyto_name', 
	        'subject', 
	        'from_email', 
	        'from_name', 
	        'returnPath', 
	        'plainText', 
	        'htmlText'    
    	);
    	
    	$mailconfig = $this->configurations->get($configpath);
//    	 debug($mailconfig);
//    	debug($configpath);
//    	debug($key);
    	$allowedMailConfig = $mailconfig;
    	foreach ($mailconfig as $key => $val){    		
	      	if (strpos($allowedMailConfig[$key],'ields.')) {
//	      	  debug(substr($allowedMailConfig[$key],7));
	          $allowedMailConfig[$key] = $this->parameters->get(substr($allowedMailConfig[$key],7));          
	        }
    		if (!in_array($key, $allowedMailConfigVars)) unset($allowedMailConfig[$key]);
    	}
    	if ($model != '')
    	foreach ($mailconfig as $key => $val){    		
	      	if (strpos($allowedMailConfig[$key],'atabasefield.')) {
	      	   if (substr($allowedMailConfig[$key],14) == 'email') {
	      	   	  $username = $this->parameters->get('username');
		          $allowedMailConfig[$key] = $model->getEmailByUsername($username);   
	      	   }
	        }
    		if (!in_array($key, $allowedMailConfigVars)) unset($allowedMailConfig[$key]);
    	}
//    	  		debug('bar');
//	    debug($allowedMailConfig);
//    	  	  debug('foo');
//	    
    	
    	return $allowedMailConfig;    	
    }

    function confirm($columnConfirmID, $confirmID) {
    	$modelClassName = tx_div::makeInstanceClassName('tx_feuserregister_model_frontenduserregistration');
      $viewClassName = tx_div::makeInstanceClassName('tx_feuserregister_view_feuserregister');
      $entryClassName = tx_div::makeInstanceClassName($this->configurations->get('config.entryClassName'));

      $translatorClassName = tx_div::makeInstanceClassName('tx_feuserregister_translator');
      tx_div::loadTcaAdditions($this->getExtensionKey());
      $functionsClassName = tx_div::makeInstanceClassName('tx_feuserregister_functions');
	    $functions = new $functionsClassName($this);
	    $functions->init($this);
  		$this->functionsObject = $functions;
      $view = new $viewClassName($this);
      
      #$this->parameters->offsetUnset('action');
      $model = new $modelClassName($this);
    	$model->load($this->parameters);
	   #die('foo');
      $this->model = &$model;
#      debug($columnConfirmID);
      //FIXME: load($columnConfirmID, $confirmID) existiert noch nicht
      $user = $this->model->loadByConfirmID($columnConfirmID, $confirmID);
      

      $view->setTemplatePath($this->configurations->get('templatePath'));
      $linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
      $linkObj = new $linkObjClassname();
      $this->cObjectWrapper = &$linkObj;
      
		$translator = new $translatorClassName($this);
		$translator->setPathToLanguageFile('EXT:feuserregister/locallang.xml');
		$this->translator = &$translator;

        // wenn $user === FALSE template feuserregister_$columnConfirmID_false 
        // wenn $user === TRUE template feuserregister_$columnConfirmID_true 
		if ($user === FALSE) {
			$this->hookHandler->call('failConfirm', $this, $columnConfirmID, $confirmID);
			$template = 'feuserregister_create_'.$columnConfirmID.'_false';
		} else {
			$this->hookHandler->call('beforeConfirm', $this, $columnConfirmID, $confirmID);
			$template = 'feuserregister_create_'.$columnConfirmID.'_true';
			// FIXME: wenn beide column IDs === 1 => User aktivieren und TS definierte Aktion ausführen
			if ($this->model->allConfirmed($columnConfirmID, $confirmID)) {
#			 debug('allConfirmed');
				$this->model->activateUser($columnConfirmID, $confirmID);
				$template = 'feuserregister_confirmation_complete';
			} else if ($this->model->UserConfirmed()){
//				       		afterUserConfirmation 
				$template = 'feuserregister_wait_for_adminconfirmation';				
			} else if ($this->model->AdminConfirmed()){
//				       		afterUserConfirmation 
				$template = 'feuserregister_wait_for_adminconfirmation';				
			}
			// FIXME: wenn eine column IDs != 1 => User nicht aktivieren und TS definierte Aktion ausführen
			$this->hookHandler->call('afterConfirm', $this, $columnConfirmID, $confirmID);
		}
		$template = $this->checkErrors($template);
#		debug($template);
		$out .= $view->renderConfirmation($template);
		$out = $translator->translate($out);
		# $out .= '<pre>'.print_r($this->stepRequirementErrors, true).'</pre>';
        return $out;
        
    }
    
    function getConfirmColumnName($confirmColumn) {
    	if ($confirmColumn == $this->doubleoptin_col_user) {
    		return $this->doubleoptin_confirmed_col_user;
    	} else if ($confirmColumn == $this->doubleoptin_col_admin){
    		return $this->doubleoptin_confirmed_col_admin;
    	}
    	return FALSE;
    }
    
    function confirmadminAction() {
    	$this->confirm('doubleoptin_code_user', $confirmID);
    }
    
    function confirmuserAction() {
    	$this->confirm('doubleoptin_code_admin', $confirmID);    	
    }
    
    function removeDot($key) {
    	if (strrpos($key, '.')) {
    		return substr($key, 0, -1);
    	}	
    }
    
    function doSpecialConfigs() {
    	
    	//fields mit anderen Feldwerten überschreiben
    	$specialConfigs = $this->configurations->get('config.special.fields.');
#    	debug($specialConfigs);
#    	debug($this->configurations->_iterator);
    	if (is_array($specialConfigs)) {
    		foreach ($specialConfigs as $key => $value) {
    			// key == fieldname
    			// value == fields.andererFeldname
    			$otherField = strpos($value, 'ields.') ? substr($value, 7) : '';
    			$defaultField = (is_array($value) && array_key_exists('default', $value)) ? $value['default'] : '';
    			
    			if ($otherField != '') {
    				$this->parameters->set($key, $this->parameters->get($otherField));
//    				t3lib_div::debug($key);
//    				t3lib_div::debug($this->parameters->get($otherField));
    			} else if ($defaultField != '') {
    					$key = $this->removeDot($key);
	    				$this->parameters->set($key, $defaultField);
#	    				debug($key);
#	    				debug($this->parameters->get($key));
    			} else {
	    			die('TypoScript config.special unsauber implementiert. Dieses die() finden sie im feuserregister Controller doSpecialConfigs().');
    			}
    		}
    	}

//    	t3lib_div::debug($specialConfigs);
    }
    
    /**
     *
     *
     */
     function checkFileType($fileType, $allowed = array()){
#      debug($allowed);
#      debug($fileType);
      foreach ($allowed as $aft){
#        debug($aft);
        if (strtoupper ($aft) == strtoupper($fileType)) {
          return TRUE;
        }        
        if (strtoupper ('image/'.$aft) == strtoupper($fileType)) {
          return TRUE;
        }
        if (strtoupper ('image/p'.$aft) == strtoupper($fileType)) {
          return TRUE;
        }
      }
        die ('Man darf nur Bilder vom Typ jpeg,jpg,gif oder png hochladen.');
     }
                  
   	/**
	 * Implementation of checkFileUpload()
	 *
	 * handles file uploads
	 *
	 */
    function checkFileUpload() {
      $designator = $this->getDefaultDesignator();
#      debug('FILES 2 START');
#      debug($_FILES);
#      debug('FILES 2 END');
  		if (isset($_FILES[$designator])){
  		   
  		  $pfad = $this->configurations->get('config.uploadFolder');
        if ($dateiname = $_FILES[$designator]['name']['image'])
        {
            $username = $this->parameters->get('username');
            if ($username == '') {
              $username = $GLOBALS["TSFE"]->fe_user->user[username];
            }
            $dateiname = $username . '_' . substr(md5(time()), 0, 4) . '_' . substr($_FILES[$designator]['name']['image'], -4);
            $_FILES[$designator]['name']['image'] = $dateiname;
            # debug($_FILES[$designator]);
            $dateiname_temp = $_FILES[$designator]['tmp_name']['image'];
            if ($dateiname_temp && $this->checkFileType($_FILES[$designator]['type']['image'], array('jpeg','jpg','gif','png')))
            {
                if (move_uploaded_file($dateiname_temp, PATH_site.$pfad.$dateiname)) {
                  if (filesize($pfad.$dateiname)<1) die ('Sie haben versucht eine leere Datei hochzuladen:"'.PATH_site.$pfad.$dateiname.'" !');
                  $dateiupload_erfolgreich = true;
                } else {
                  die('move_uploaded_file liefert false zurueck. filesize tmp_file: '.$dateiname_temp.' - ' . (filesize($dateiname_temp)));
                }
            }
        }
      }    
#      debug('FILES 2 START');
#      debug($_FILES);
#      debug('FILES 2 END');
    }
    
    /**
     * Implementation of handleProcessType
     *
     * sets disabled recording to the processType
     */                   
     function handleProcessType($insertArray = array()) {
        // pT = processType
        $pT = $this->configurations->get('config.registerProcessType');
      
        #######################
        ##
        ## registerProcessType:
        ## registration without requirement: 1
        ## registration with    requirement: admin confirmation: 2
        ## registration with    requirement: user confirmation: 3
        ## registration with    requirement: admin & user confirmation: 4
        ## registration with	password will generated by system and sent to users given email address: 5
        ##
        #######################      
        switch ($pT) {
          case 1:
            return $insertArray;
            break;
          case 2:          
          case 3:
          case 4:
            $insertArray['disable'] = 1;
            break;
          case 5:
          	$insertArray['password'] = $this->randPassword;
          	$pT = $this->parameters->set('password_input', $insertArray['password']);
          	break;
          default:
            // error
        }
#        debug($insertArray);
        return $insertArray;
     }
     
     /**
      * Implementation of getConfirmationMarker
      *
      */
      function addConfirmationMarker($markerArray = array()){
        $markerArray['userHREF'] = $this->getConfirmLinkHref('user');
        $markerArray['adminHREF'] = $this->getConfirmLinkHref('admin');
#        $markerArray[''] = '';
        return $markerArray;
      }                 
                   
   	/**
	 * Implementation of createAction()
	 *
	 * creates a new frontend user
	 *
	 */
    function createAction() {
#    	debug($this->parameters->_iterator);
//    	debug('create');
    	$modelClassName = tx_div::makeInstanceClassName('tx_feuserregister_model_frontenduserregistration');
      $viewClassName = tx_div::makeInstanceClassName('tx_feuserregister_view_feuserregister');
      $entryClassName = tx_div::makeInstanceClassName($this->configurations->get('config.entryClassName'));

      $translatorClassName = tx_div::makeInstanceClassName('tx_feuserregister_translator');
      $this->checkFileUpload();    

      tx_div::loadTcaAdditions($this->getExtensionKey());
      $linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
      $linkObj = new $linkObjClassname();
//        debug($this->configurations->_iterator);
      $this->cObjectWrapper = $linkObj;
      $functionsClassName = tx_div::makeInstanceClassName('tx_feuserregister_functions');
	    $functions = new $functionsClassName();
	    $functions->init($this);
		$this->functionsObject = $functions;
        $view = new $viewClassName($this);
#    	debug($this->parameters->_iterator);

        $this->doSpecialConfigs();
#    	debug($this->parameters->_iterator);
        
        #$this->parameters->offsetUnset('action');
        $model = new $modelClassName($this);
		$model->load($this->parameters);
        $this->model = &$model;
        
        //FIXME: DEBUG TRUE in IF        
    	$this->initStepConfig();
#    	debug($this->currentStep);
    	$this->validateStep();
    	$this->includeExtJS();
#    	debug($this->parameters->_iterator);
        $saved = FALSE;
//        debug($this->isSaveStep());
		#  debug($this->errors);
        
        if ($this->isSaveStep() === TRUE){
        

        	if (!$this->exists_username()) {
//        		t3lib_div::debug('username existst false');
        		$res = $model->insert($this->parameters);
        	} else {
        		$this->errors['username'] = 1;
        	}

        	if ($res){
        		$saved = TRUE;
        		#t3lib_div::debug('saved!');
        		//FIXME: Abfrage einbauen welches Handling / welche Mails erwünscht ist
        		$emailConfigs = $this->configurations->get('config.email.');
        		if (count($emailConfigs)){
//        			debug($emailConfigs);
      				foreach ($emailConfigs as $key => $value){
#            	 		 t3lib_div::debug($emailConfigs);	
	          	 		 $templateNameHtml = $this->configurations->get('config.email.'.$key.'templateNameHtml');
	        			$templateNameText = $this->configurations->get('config.email.'.$key.'templateNameText');
	        			
//	        			debug($this->configurations->get('config.email.'.$key.'templatePath'));
				        $view->setTemplatePath($this->configurations->get('config.email.'.$key.'templatePath'));
#				        debug($this->configurations->get('config.email.'.$key.'templatePath'));
				        $pathToLanguageFile = $this->configurations->get('config.email.'.$key.'pathToLanguageFile');
				        
				        
    						$translator = new $translatorClassName($this);
    						$translator->setPathToLanguageFile('EXT:feuserregister/locallang.xml');
    						$this->translator = &$translator;
//    	        			debug('before fetch');	
    						$mailConfig = $this->fetchMailConfig('config.email.'.$key);
//    						debug($mailConfig);
//    						debug('before plain');
    						$plainTextMail = $view->renderMail($templateNameText);
//    						debug('bef html');
    						$htmlTextMail = $view->renderMail($templateNameHtml);
//							debug('before trans');    								  
    						$mailConfig['plainText'] = $translator->translate($plainTextMail);
    						$mailConfig['htmlText'] = $translator->translate($htmlTextMail);
                #  t3lib_div::debug($plainTextMail);	
                #  t3lib_div::debug($mailConfig['htmlText']);	
    						#  debug($this->errors);
#    						debug($templateNameHtml);
#    						debug($templateNameText);
    						
//    						debug($mailConfig);
    						
    						$this->sendMail($mailConfig);		        					
        			}
        		}
        	} else {
            $this->lowerStep();
          }
        }
//		debug('mail sent');
		$model->load($this->parameters);
        for($model->rewind(); $model->valid(); $model->next()) {
            $entry = new $entryClassName($model->current(), $this);
            $view->append($entry);
        }
        $view->setTemplatePath($this->configurations->get('templatePath'));
     #   t3lib_div::debug($this->configurations->get('templatePath'));
		$translator = new $translatorClassName($this);
		$translator->setPathToLanguageFile('EXT:feuserregister/locallang.xml');
		$this->translator = &$translator;
#		t3lib_div::debug($this->translator->LOCAL_LANG);
		#t3lib_div::debug($this->parameters->_iterator);
		if ($this->isSaveStep() && $saved) {
			#t3lib_div::debug('save');
			$template = 'feuserregister_after_save';
		} else if ($this->isSaveStep() && !$saved) {
			$template = 'feuserregister_save_failed';
		} else {
//			debug($this->configurations);
			if ($this->showPreview()) {
				if ($this->configurations->get('config.useSingleTemplate') == 1) {
					$template = 'feuserregister_preview';
				} else {
					$next = $this->getNextStep();
					$current = $this->getCurrentStep();				
					$template = 'feuserregister_preview_step_'.$this->_removeDot($this->origKeysMapping[$next]);
				}
			} else {
				if ($this->configurations->get('config.useSingleTemplate') == 1) {
					$template = 'feuserregister_create';
				} else {
					$next = $this->getNextStep();
					$current = $this->getCurrentStep();				
					$template = 'feuserregister_create_step_'.$this->_removeDot($this->origKeysMapping[$next]);
				}
			}
		}
		
		$template = $this->checkErrors($template);
//		debug('TEMPLATE:');
//		t3lib_div::debug($GLOBALS['TSFE']);
//		debug($template);
//		t3lib_div::debug($this->origKeysMapping);
//		t3lib_div::debug($next);

		if ($template == 'feuserregister_after_save' && $this->configurations->get('config.redirectAfterLoginMode')) {
			$mode = $this->configurations->get('config.redirectAfterLoginMode');
//			debug($mode);
			switch ($mode) {
				case 'page_intern':
					if ($this->configurations->get('config.redirectRegistrationValue')) {
						$pid = $this->configurations->get('config.redirectRegistrationValue');
//						debug($pid);
						$linkObj= $linkObj->destination($pid);
						
						$linkObj->redirect();
					}					
				break;
				case 'page_extern':
					if ($this->configurations->get('config.redirectRegistrationValue')) {
						$redirect_url = $this->configurations->get('config.redirectRegistrationValue');
					}					
				break;
				default:
			}
#			header('Location: '.t3lib_div::locationHeaderUrl($this->redirectUrl));
//			exit;
		}
//		debug('dfr');
		$out .= $view->render($template);

		$out = $translator->translate($out);
		#debug($GLOBALS[TSFE]);
		# $out .= '<pre>'.print_r($this->stepRequirementErrors, true).'</pre>';
        return $this->pi_wrapInBaseClass($out);
    }
    
	/**
	 * Wraps the input string in a <div> tag with the class attribute set to the prefixId.
	 * All content returned from your plugins should be returned through this function so all content from your plugin is encapsulated in a <div>-tag nicely identifying the content of your plugin.
	 *
	 * @param	string		HTML content to wrap in the div-tags with the "main class" of the plugin
	 * @return	string		HTML content wrapped, ready to return to the parent object.
	 */
    var $prefixId;
    var $extKey;
	function pi_wrapInBaseClass($str)	{
		$this->extKey = $this->getDefaultDesignator();
		$this->prefixId = $this->extKey . '_' . $this->action;
		$content = '<div class="'.str_replace('_','-',$this->prefixId).'">
		'.$str.'
	</div>
	';

		if(!$GLOBALS['TSFE']->config['config']['disablePrefixComment'])	{
			$content = '


	<!--

		BEGIN: Content of extension "'.$this->extKey.'", plugin "'.$this->prefixId.'"

	-->
	'.$content.'
	<!-- END: Content of extension "'.$this->extKey.'", plugin "'.$this->prefixId.'" -->

	';
		}
		return $content;
	}
    
    
   	/**
	 * Implementation of confirmMessageRequestAction()
	 *
	 * creates a new frontend user
	 *
	 */
    function confirmMessageRequestAction() {
#    	debug('confirmMessageRequest');
     	$modelClassName = tx_div::makeInstanceClassName('tx_feuserregister_model_frontenduserregistration');
        $viewClassName = tx_div::makeInstanceClassName('tx_feuserregister_view_feuserregister');
        $entryClassName = tx_div::makeInstanceClassName($this->configurations->get('config.entryClassName'));

        $translatorClassName = tx_div::makeInstanceClassName('tx_feuserregister_translator');

        tx_div::loadTcaAdditions($this->getExtensionKey());
        $linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
        $linkObj = new $linkObjClassname();
        $this->cObjectWrapper = &$linkObj;
        $functionsClassName = tx_div::makeInstanceClassName('tx_feuserregister_functions');
	    $functions = new $functionsClassName($this);
	    $functions->init($this);
		$this->functionsObject = $functions;
        $view = new $viewClassName($this);
        
        
        #$this->parameters->offsetUnset('action');
        $model = new $modelClassName($this);
		$model->load($this->parameters);
		
        $this->model = &$model;
        
    	
        $emailParamSet = FALSE;
        if (!$this->parameters->get('feuserregister_submitbutton'.$this->action))
        $usernameParamSet = FALSE;
        if ($submittedActionEqualsRunAction)
        if ($this->parameters->get('email') || $this->parameters->get('username')){
        	$email = ($this->parameters->get('email')) ? TRUE : FALSE;
        	$emailParamSet = ($email) ? TRUE : FALSE;
        	$usernameParamSet = ($email) ? FALSE : TRUE;
        	
        	$param = ($email) ? $this->parameters->get('email') : $this->parameters->get('username');
        	$linkHref = $this->getConfirmLinkHrefByUsername($param);
       		#t3lib_div::debug($linkHref);
       		$this->additionalMarker['confirm_href_user'] = $linkHref;
       		$emailIsValid = FALSE;
       		$usernameIsValid = FALSE;
       		if ($linkHref){
	        	$emailIsValid = TRUE;
	        	$usernameIsValid = TRUE;
	        	if ($email) {
	        		$model->loadByEmail($this->parameters->get('email'));
        		} else {
	        		$model->loadByUsername($this->parameters->get('username'));        			
        		}
        		$this->model = &$model;
        		//FIXME: Abfrage einbauen welches Handling / welche Mails erwünscht ist
        		$configpath = 'config.confirmation.email.confirmationResendRequest.';
        		$emailConfigs = $this->configurations->get($configpath);
        		if (count($emailConfigs)){
      				foreach ($emailConfigs as $key => $value){
	          			$templateNameHtml = $this->configurations->get($configpath.$key.'templateNameHtml');
	        			$templateNameText = $this->configurations->get($configpath.$key.'templateNameText');
	        			
	        			
				        $view->setTemplatePath($this->configurations->get($configpath.$key.'templatePath'));
				        
				        $pathToLanguageFile = $this->configurations->get($configpath.$key.'pathToLanguageFile');
				        
				        $linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
				        $linkObj = new $linkObjClassname();
				        $this->cObjectWrapper = &$linkObj;
				        
						$translator = new $translatorClassName($this);
						$translator->setPathToLanguageFile('EXT:feuserregister/locallang.xml');
						$this->translator = &$translator;
	        			
						$mailConfig = $this->fetchMailConfig($configpath.$key, $this->model);
						
						$username = ($email) ? $this->model->getUsernameByEmail : $this->parameters->get('username');
#						$emailad = ($email) ? $this->parameters->get('email') : $this->model->getEmailByUsername($username);
						
						$markerArray = $this->additionalMarker;
						$markerArray['username'] = $username;
						$markerArray['confirm_link'] = '<a href="'.$markerArray['confirm_href_user'].'">Confirm</a>';
						$markerArray['password'] = $this->model->getFieldByUsernameOrEmail($username, 'password');
#						debug($markerArray);
						$plainTextMail .= $view->renderConfirmationRequestMail($templateNameText, $markerArray);
						$htmlTextMail .= $view->renderConfirmationRequestMail($templateNameHtml, $markerArray);
						
						$mailConfig['plainText'] = $translator->translate($plainTextMail);
						$mailConfig['htmlText'] = $translator->translate($htmlTextMail);
#						debug($mailConfig);
						$this->sendMail($mailConfig);		        					
        			}
        		}
        	}
        }
//		t3lib_div::debug('STEPS nach savestep');
//		t3lib_div::debug($this->getSteps());
		$model->load($this->parameters);
        $view->setTemplatePath($this->configurations->get('templatePath'));
        
		$translator = new $translatorClassName($this);
		$translator->setPathToLanguageFile('EXT:feuserregister/locallang.xml');
		$this->translator = &$translator;
#		t3lib_div::debug($this->translator->LOCAL_LANG);
		#t3lib_div::debug($this->parameters->_iterator);
		if (($emailParamSet || $usernameParamSet) && $emailIsValid) {
			#t3lib_div::debug('save');
			if ($emailParamSet) {
				$template = 'feuserregister_after_resend_confirmation_emailgiven';				
			} else {
				$template = 'feuserregister_after_resend_confirmation_usernamegiven';			
			}
		} else if (($emailParamSet && !$emailIsValid) || ($usernameParamSet && !$usernameIsValid)) {
			$template = 'feuserregister_after_resend_failed';
		} else {
			$template = 'feuserregister_resend_confirmation';
		}
		$template = $this->checkErrors($template);
#		t3lib_div::debug('TEMPLATE:');
//		t3lib_div::debug($GLOBALS['TSFE']);
#		t3lib_div::debug($template);
//		t3lib_div::debug($this->origKeysMapping);
//		t3lib_div::debug($this->configurations->_iterator);
		$out .= $view->renderConfirmationResendRequest($template);
#		debug($out);
		$out = $translator->translate($out);
#		debug($template);
		# $out .= '<pre>'.print_r($this->stepRequirementErrors, true).'</pre>';
		return $this->pi_wrapInBaseClass($out);		
    }

  function getEditConfigSeperateLabelMarker() {
    return $this->configurations->get('config.seperate_label_markers');
  }


	/**
	 * Implementation of editAction()
	 *
	 * edits a frontend user
	 *
	 */
    function editAction() {
#    global $_FILES;
#      debug('FILES START');
#      debug($_FILES);
#      debug('FILES END');
    #	t3lib_div::debug('edit');
    	$this->isEditMode = TRUE;
    	$modelClassName = tx_div::makeInstanceClassName('tx_feuserregister_model_frontenduserregistration');
        $viewClassName = tx_div::makeInstanceClassName('tx_feuserregister_view_feuserregister');
        $entryClassName = tx_div::makeInstanceClassName($this->configurations->get('config.entryClassName'));

        $translatorClassName = tx_div::makeInstanceClassName('tx_feuserregister_translator');
      	$this->checkFileUpload();
        tx_div::loadTcaAdditions($this->getExtensionKey());
      	$linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
      	$linkObj = new $linkObjClassname();
#        debug($this->parameters->_iterator);
#        debug($GLOBALS['TSFE']->fe_user->user);
      $this->cObjectWrapper = $linkObj;
        $functionsClassName = tx_div::makeInstanceClassName('tx_feuserregister_functions');
	    $functions = new $functionsClassName($this);
	    $functions->init($this);
		$this->functionsObject = $functions;
        $view = new $viewClassName($this);
        
        #$this->parameters->offsetUnset('action');
        $model = new $modelClassName($this);
        $feuserUID = $GLOBALS['TSFE']->fe_user->user['uid'];
        $this->parameters->set('uid', $feuserUID);
//		die('foo');
        $model->loadByUID($feuserUID);
		
        $this->model = &$model;
        
        //FIXME: DEBUG TRUE in IF        
#    	$this->initStepConfig();
    	$this->includeExtJS();
        $saved = FALSE;
#        debug($this->isEditSaveStep());
        if ($this->isEditSaveStep() === TRUE){        	
	        $res = $model->update($this->parameters);        	
        	if ($res){
        		$saved = TRUE;
        		#t3lib_div::debug('saved!');
        	}
        }
//        		//FIXME: Abfrage einbauen welches Handling / welche Mails erwünscht ist
//        		$emailConfigs = $this->configurations->get('config.email.');
//        		if (count($emailConfigs)){
//      				foreach ($emailConfigs as $key => $value){
//#            	t3lib_div::debug($emailConfigs);	
//	          			$templateNameHtml = $this->configurations->get('config.email.'.$key.'templateNameHtml');
//	        			$templateNameText = $this->configurations->get('config.email.'.$key.'templateNameText');
//	        			
//	        			
//				        $view->setTemplatePath($this->configurations->get('config.email.'.$key.'templatePath'));
//				        
//				        $pathToLanguageFile = $this->configurations->get('config.email.'.$key.'pathToLanguageFile');
//				        
//				        $linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
//				        $linkObj = new $linkObjClassname();
//				        $this->cObjectWrapper = &$linkObj;
//				        
//						$translator = new $translatorClassName($this);
//						$translator->setPathToLanguageFile('EXT:feuserregister/locallang.xml');
//						$this->translator = &$translator;
//	        			
//						$mailConfig = $this->fetchMailConfig('config.email.'.$key);
//						
//						$plainTextMail .= $view->renderMail($templateNameHtml);
//						$htmlTextMail .= $view->renderMail($templateNameText);
//						
//						$mailConfig['plainText'] = $translator->translate($plainTextMail);
//						$mailConfig['htmlText'] = $translator->translate($htmlTextMail);
//            	#t3lib_div::debug($plainTextMail);	
//            	#t3lib_div::debug($mailConfig['htmlText']);	
//						
//						$this->sendMail($mailConfig);		        					
//        			}
//        		}
//        	} else (die(mysql_error()));
//        }
//		t3lib_div::debug('STEPS nach savestep');
//		t3lib_div::debug($this->getSteps());

        for($model->rewind(); $model->valid(); $model->next()) {
            $entry = new $entryClassName($model->current(), $this);
            $view->append($entry);
        }
        $view->setTemplatePath($this->configurations->get('templatePath'));
        $linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
        $linkObj = new $linkObjClassname();
        $this->cObjectWrapper = &$linkObj;
        
		$translator = new $translatorClassName($this);
		$translator->setPathToLanguageFile('EXT:feuserregister/locallang.xml');
		$this->translator = &$translator;
#		t3lib_div::debug($this->translator->LOCAL_LANG);
		#t3lib_div::debug($this->parameters->_iterator);
		if ($this->isEditSaveStep() && $saved) {
			#t3lib_div::debug('save');
			$template = 'feuserregister_edit_after_save';
		} else if ($this->isEditSaveStep() && !$saved) {
			$template = 'feuserregister_save_failed';
		}else {
			#t3lib_div::debug('create');
			$template = 'feuserregister_edit';
		}
		$template = $this->checkErrors($template);
#		debug($template);
		$out .= $view->renderEdit($template);
		$out = $translator->translate($out);
		
		# $out .= '<pre>'.print_r($this->stepRequirementErrors, true).'</pre>';
        return $out;
    }
    
    function setError($key, $value) {
    	$this->errors[$key] = $value;
    	if ($key == 'mysqlInsert') die($value);
    }
    
    function getEditFieldsConfig() {
    	return $this->configurations->get('config.fields.');
    }
    
    function isEditMode() {
      //TODO: Change this to make steps possible
      return $this->isEditMode;
    }
    
    function getModelVariableNoErrors() {
      return $this->model->noErrors;
    }
    
    function lowerStep() {		
    	$stepParam = $this->parameters->offsetExists('step');
    	if (!$stepParam) {
    		if (!$this->parameters->get('feuserregister_submitbutton_'.$this->action)) {
				return TRUE;    			
    		}
    		die('hier darf er niemals nicht rein kommen');
    	}
    	else {
	        $cur = $this->currentStep;
	        $unset = FALSE;
	        if ($cur == 0) $unset = TRUE;
	        $cur--;
	        $this->parameters->offsetSet('step', $cur);
	        $this->stepLowered = TRUE;
	        if ($unset) $this->parameters->offsetUnset('step');
      }
    }
    
    function isEditSaveStep(){
    	$tmp = $this->parameters->get('saveedit');
    	if ($tmp == 'yes') {
    		return TRUE;
    	}
    	return FALSE;
    }
    
    
    function getActionURL(){
    	return 'index.php?id='.$GLOBALS['TSFE']->id.'&no_cache=1';
    }
    
    function checkErrors($template){
    	if (count($this->errors)) {
    		if (array_key_exists('user_aleady_exists_reload_error', $this->errors)) {
    			return 'feuserregister_error_user_exists_reload_error';
    		}
    		if (array_key_exists('username', $this->errors)) {
    			return 'feuserregister_error_user_exists';
    		}
    	}
    	return $template;
    }

    /**
     * Implementation of fetchPreviewFieldConfig($stepConfig)
     * 
     * merges the configuration of normal view mode and preview view mode. 
     * 
     * 
     */
    function fetchPreviewFieldConfig($stepConfig){
#    	t3lib_div::debug($stepConfig);
    	if (array_key_exists('preview.', $stepConfig)){
	    	if (!array_key_exists('fields.', $stepConfig['preview.'])) return $stepConfig['fields.'];
    	} else {
    		return $stepConfig['fields.'];
    	}
    	$normal = $stepConfig['fields.'];
    	$preview = $stepConfig['preview.']['fields.'];
    	return array_merge($normal, $preview);
    }
    
    function _getNewJSRequiredArrayFieldID(){
    	return $this->jsRequiredArrayCounter++;
    }
    
    /**
     * configures the JS we need for special checks
     * 
     * @return boolean
     */
    function includeExtJSConfig()
    {
    	//FIXME: fetches all configuration for special checks to the element of the current step and more
		$color = 'yellow';
    	$className = 'feuserregisterrequiredfields';
    	
    	
    	
		foreach ($this->getSecondInputCheckIDs() as $id){
			
		}
				
    	
#    	$js .= '</script>';
  #  	t3lib_div::debug($js);
    	$GLOBALS['TSFE']->additionalJavaScript['additionalHeaderData'] = $js;
    	$GLOBALS['TSFE']->additionalHeaderData['additionalHeaderData'] = '
    		<script type="text/javascript" src="typo3conf/ext/feuserregister/lib/validation/scriptaculous/lib/prototype.js"></script>
    		<script type="text/javascript" src="typo3conf/ext/feuserregister/lib/validation/scriptaculous/src/effects.js"></script>
    		<script type="text/javascript" src="typo3conf/ext/feuserregister/lib/validation/validation.js"></script>
    		';
    	if ($this->configurations->get('config.validationStyles.file')) {
        	$GLOBALS['TSFE']->additionalHeaderData['additionalHeaderData'] = '
    		<script type="text/javascript" src="typo3conf/ext/feuserregister/lib/validation/scriptaculous/lib/prototype.js"></script>
    		<script type="text/javascript" src="typo3conf/ext/feuserregister/lib/validation/scriptaculous/src/effects.js"></script>
    		<script type="text/javascript" src="typo3conf/ext/feuserregister/lib/validation/validation.js"></script>
    		<script type="text/javascript" src="'.$this->configurations->get('config.validationStyles.file').'"></script>
        	';
      	}
      	$GLOBALS['TSFE']->additionalHeaderData['JSFormValidate'] = '';
      	if ($this->validationMethods) {
	      	$GLOBALS['TSFE']->additionalHeaderData['JSFormValidate'] .= '
	    		<script type="text/javascript">'.$this->validationMethods.'</script>
	    	';
      	}
      	$this->belowForm = $GLOBALS['TSFE']->additionalHeaderData['JSFormValidate'];
      	$GLOBALS['TSFE']->additionalHeaderData['JSFormValidate'] = '';
    }
    
    
    
    /**
     * implements the identificator of fields
     * 
     * @return string
     */
    function getFieldId($key, $extra = '')
    {
    	if ($extra != '') $extra = '_validate';
    	$designator = $this->getDesignator();
    	return $designator.'_'.$key.$extra;
    }
    
    
    /**
     * 
     * @return array
     */
    function getSecondInputCheckIDs()
    {
    	debug('foo');
    	$resultArray = array();
    	$steps = $this->steps;
#    	$nextStep = $this->getNextStep();
      $nextStep = $this->currentStep;
      $nextStep++;
#    	$flippedOrigArray = array_flip($this->origKeysMapping);
#    	$stepKey = $flippedOrigArray[$this->_getStepKey()];
#		debug($nextStep);
    if ($this->isEditMode()){
#      debug('editmode');
      $config = $this->configurations->get('config.');
      $nextStep = 0;
      $steps[$nextStep] = $config;
    }
#     debug($nextStep);
#     debug ($steps);
		if (is_array($steps) || is_object($steps)) {
			if (array_key_exists($nextStep, $steps)) {
			   	foreach ($steps[$nextStep]['fields.'] as $key => $value){
		    		if (!array_key_exists('validate.', $value)) continue;
		
		    		$key = $this->_removeDot($key);
		    		$fieldId = $this->getFieldId($key);
		
		    		$resultArray[] = $fieldId;
		    		$this->validationMethods[$fieldId]['fieldName'] = $key;
		    		$this->validationMethods[$fieldId]['jsRequiredArrayFieldID'] = $this->_getNewJSRequiredArrayFieldID();
		    		if (array_key_exists('secondInputField', $value['validate.'])){

		    			
		    			
						    			
						$err_msg = addslashes($value['validate.']['err_message']);
						if (!$err_msg) $err_msg='Your password must be more than 6 characters and not be \\\'password\\\' or the same as your name';
						#debug($err_msg);
						$err_msg = $this->cObjectWrapper->cObject->stdWrap($err_msg, $value['validate.']['err_message.']);
						$minlen = $value['validate.']['minlen'];
						//7;
						$notOneOf = "'" . implode('\',\'',explode(',', addslashes($value['validate.']['notOneOf']))) . "'";
						$notEqualToField = $this->getFieldId($value['validate.']['notEqualToField']);
          	$designator = $this->getDesignator();
          	if ($designator.'_' == $notEqualToField) {
          	 $notEqualToField = '';
            } else {
              $notEqualToField = "notEqualToField : '".$notEqualToField."'"; 
            }
#						if ($notEqualToField != '') $notEqualToField = 
						$err_confirm_msg = addslashes($value['validate.']['err_confirm_message']);
						if (!$err_confirm_msg) $err_confirm_msg='Your confirmation password does not match your first password, please try again.';
						$err_confirm_msg = $this->cObjectWrapper->cObject->stdWrap($err_confirm_msg, $value['validate.']['err_confirm_message.']);
						$equalToField = $fieldId;
							
				    	$js_arr[] .= "
								['validate-".$fieldId."', '".$err_msg."', {
									minLength : ".$minlen.",
									notOneOf : [".$notOneOf."],
							 		".$notEqualToField."
							 	}],
							 	['validate-".$fieldId."-confirm', '".$err_confirm_msg."', {
							 		equalToField : '".$equalToField."'
							 	}]
						";
		    		}
		    	}
			}				
		}
		if (is_array($js_arr)){
			$js = '
				function formCallback(result, form) {
					console.log("valiation callback for form \'" + form.id + "\': result = " + result);
				}
				var valid = new Validation(\''.$this->getDesignator().'form\', {immediate : true, onFormValidate : formCallback});
				
				Validation.addAllThese(['.trim(implode(', ',$js_arr)).']);';
		} else {
			$js = '
				function formCallback(result, form) {
					console.log("valiation callback for form \'" + form.id + "\': result = " + result);
				}
				var valid = new Validation(\''.$this->getDesignator().'form\', {immediate : true, onFormValidate : formCallback});';
		}
		$this->validationMethods = $js;
    	return $resultArray;
    }
    
    
    /**
     * 
     * 
     */
    function includeExtJS(){
    	if ($this->configurations->get('config.useJSValidation') && !$this->extjsIncluded){
    		$this->includeExtJSConfig();
    	}
    	$this->extjsIncluded = TRUE;
    }
    
	/**
	 * Implementation of editAction()
	 *
	 * edits a frontend user
	 *
	 */
    function saveAction() {
    	$modelClassName = tx_div::makeInstanceClassName('tx_feuserregister_model_frontenduserregistration');
        $viewClassName = tx_div::makeInstanceClassName('tx_feuserregister_view_feuserregister');
        $entryClassName = tx_div::makeInstanceClassName($this->configurations->get('config.entryClassName'));

        $translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
    	
        tx_div::loadTcaAdditions($this->getExtensionKey());
        $functionsClassName = tx_div::makeInstanceClassName('tx_feuserregister_functions');
	    $functions = new $functionsClassName($this);
	    $functions->init();
		$this->functionsObject = $functions;
		
        $view = new $viewClassName($this);
        
        $model = new $modelClassName($this);
        #$this->parameters->offsetUnset('action');
        $model->insert($this->parameters);        	
        
#        $model->load($this->parameters);
        
        for($model->rewind(); $model->valid(); $model->next()) {
            $entry = new $entryClassName($model->current(), $this);
            $view->append($entry);
        }
        $view->setTemplatePath($this->configurations->get('templatePath'));
        $linkObjClassname = tx_div::makeInstanceClassName('tx_lib_link');
        $linkObj = new $linkObjClassname();
        $this->cObjectWrapper = &$linkObj;
        
		$translator = new $translatorClassName($this, $view);
		$translator->setPathToLanguageFile('EXT:feuserregister/locallang.xml');
		$this->translator = &$translator;

		$out .= $view->render('feuserregister_save');
		$out = $translator->translate($out);
        return $out;
    }

	/**
	 * Implementation of validateEmailAction()
	 *
	 * validates an emailadress with a unique id
	 *
	 */
    function validateEmailAction() {
        $modelClassName = tx_div::makeInstanceClassName('tx_feuserregister_model_frontenduserregistration');
        $viewClassName = tx_div::makeInstanceClassName('tx_feuserregister_view_feuserregister');
        $entryClassName = tx_div::makeInstanceClassName($this->configurations->get('entryClassName'));
		$translatorClassName = tx_div::makeInstanceClassName('tx_lib_translator');
        $view = new $viewClassName($this);
        $model = new $modelClassName($this);
        $model->load($this->parameters);
        for($model->rewind(); $model->valid(); $model->next()) {
            $entry = new $entryClassName($model->current(), $this);
            $view->append($entry);
        }
        $view->setPathToTemplateDirectory($this->configurations->get('templatePath'));
        $view->render('feuserregister_create');
		$translator = new $translatorClassName($this, $view);
		$out = $translator->translateContent();
        return $out;
    }
    
    function getKeysToValidate() {
    	$stepConfig = $this->steps[$this->currentStep]['fields.'];
    	$keysToValidate = array();
    	foreach ($stepConfig as $key => $valuesArray) {
    		if (!array_key_exists('validate.', $valuesArray)) continue;
    		$keysToValidate[] = $key; 	
    	}
    	return $keysToValidate;
    }
    
    function getValidationMethods($config){
    	$methods = explode(',',$config['validate.']['validationMethod']);
    	$tmp = $methods;
    	unset($methods);
    	foreach ($tmp as $method) {
			$methods[] = trim($method);
    	}
    	return $methods;
    }
    
    function removeNotAllowedFunctionChars($vm){
    	$vm = str_replace('-', '', $vm);
    	return $vm;
    }
    
    function validateStep() {
    	$validationsPassed = TRUE;
    	if (!$this->parameters->get('feuserregister_submitbutton_'.$this->action)) {
    		$this->lowerStep();
    		return TRUE;
    	}
    	if (!$this->validateCaptcha() && $this->parameters->get('feuserregister_submitbutton')) $validationsPassed = FALSE;
    	if ($this->isAfterPreview()) return TRUE;
    	$stepParam = $this->parameters->offsetExists('step');
    	$keysToValidate = $this->getKeysToValidate();
//    	debug($this->parameters->_iterator);
    	if ($stepParam && !empty($keysToValidate)) {
#    		debug($keysToValidate);
    		foreach ($keysToValidate as $key) {
	    		$config = $this->steps[$this->currentStep]['fields.'][$key];
	    		$validationMethods = $this->getValidationMethods($config);
	    		$key = substr($key,0,-1);
	    		#debug($validationMethods);
	    		foreach ($validationMethods as $vm) {
	    			$extraValidationParam = '';
	    			if ($config['validate.'][$vm]) $extraValidationParam = $config['validate.'][$vm];
	    			$vm = $this->removeNotAllowedFunctionChars($vm);
	    			$method = 'validationMethod_'.$vm;
	    			$fieldvalue = $this->parameters->get($key);
	    			
	    			#	    			debug(method_exists ($this, $method));
	    			if (
	    				(method_exists($this, $method) && !$this->$method($key, $fieldvalue, $extraValidationParam))
	    				||
	    				($this->hookHandler->call('addExtraValidationMethods', $this, $method, $key, $fieldvalue, $extraValidationParam))
	    				) 
	    				{
		    				$validationsPassed = FALSE;
		    				if ($method == 'validationMethod_validateEquals') {
	#	    					$config['validate.']['err_confirm_message'] = $this->cObjectWrapper->cObject->stdWrap($config['validate.']['err_confirm_message'], $config['validate.']['err_confirm_message.']);
								$this->stepRequirementErrors[$key.'_validate'] = $config['validate.']['err_confirm_message'];	    						    					
		    				} else {
	#	    					debug($config);
	#	    					debug($config['validate.']['err_message']);
	#							$config['validate.']['err_message'] = $this->cObjectWrapper->cObject->stdWrap($config['validate.']['err_message'], $config['validate.']['err_message.']);	    					
								$this->stepRequirementErrors[$key] = $config['validate.']['err_message'];	    					
		    				}
	#	    				debug('validation failed');
//		    				debug($key);
	#	    				debug($fieldvalue); 
	#	    				debug($method);
	    				}
	    		}
	    	}    	
    	}
//    	debug($this->stepRequirementErrors);
    	//Nach einer Preview ist Validierung nicht notwendig
    	if (!$this->isAfterPreview && !$validationsPassed) {
	    	$this->showPreview = FALSE;	
	    	$this->lowerStep();
	    	$this->validationsPassed = FALSE;
	    	return FALSE;
    	}
    	return TRUE;

#		debug($keysToValidate);
#    	debug('validateStep start');
#    	debug($this->stepKey);
#    	debug($this->currentStep);
#    	debug($this->origKeysMapping);
#    	debug($this->steps);
#    	debug('validateStep end');
    }

    /**
     * 
     * VALIDATION METHODS
     *  
     */

     function validateCaptcha() {
	     # get captcha sting
		session_start();
		$captchaStr = $_SESSION['tx_captcha_string'];
		
#			debug($captchaStr);
		
		$_SESSION['tx_captcha_string'] = '';
		if ($captchaStr != '' && $captchaStr != $this->parameters->get('captcha_input')) {
			//error handling
			$this->stepRequirementErrors['captcha_input_error'] = 'captcha_input_error';
			$this->parameters->offsetUnset('captcha_input');			
			return FALSE;
		}
		return TRUE;
     }
    
    /**
     * 
     * method validationMethod_validateRequired($field)
     *
     *	@var $field fieldname
     */
    function validationMethod_required($fieldname, $fieldvalue) {
//    	debug($fieldname);
//    	debug($fieldvalue);
    	return (isset($fieldvalue) && (!empty($fieldvalue) || $fieldvalue === '0'));
    }

    /**
     * 
     * method validationMethod_validateemail($field)
     *
     *	@var $field fieldname
     */
    function validationMethod_validateemail($fieldname, $fieldvalue) {
#    	debug($fieldname);
#    	debug($fieldvalue);
		$fieldvalue = trim($fieldvalue);
    	return (!empty($fieldvalue) && $this->cObjectWrapper->cObject->checkEmail($fieldvalue));
    }

    /**
     * 
     * method validationMethod_validateEquals($field)
     *
     *	@var $field fieldname
     */
    function validationMethod_validateEquals($fieldname, $fieldvalue) {
#    	debug($fieldname);
#    	debug($fieldvalue);
		$toCompare = $this->parameters->get($fieldname.'_validate');
#		debug(array('foo'));
#		debug($toCompare);
#		debug($fieldvalue);
#		debug(array('bar'));
    	return (strcmp($fieldvalue, $toCompare) === 0);
    }

    /**
     * 
     * method validationMethod_validateEquals($field)
     *
     *	@var $field fieldname
     */
    function validationMethod_validateDate($fieldname, $fieldvalue) {
		if(empty($fieldvalue)) {  
			return FALSE; 
		}
		$date = trim($fieldvalue);
		if( strlen($date) == 8 ) {
			$date = substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6,2) ;
		}
		$suchmuster = '/^\d{2}-\d{2}-(\d{4})$/';
		preg_match($suchmuster, $date, $retval);
		if ($retVal == 0) return FALSE;
		return TRUE;
    }
    
    /**
     * 
     * method validationMethod_validateUnique($field)
     *
     *	@var $field fieldname
     */
    function validationMethod_validateunique($fieldname, $fieldvalue) {
#    	debug($fieldname);
#    	debug($this->parameters->_iterator);
    	$unique = $this->model->isUniqueValue($fieldname, $fieldvalue);
    	return $unique;
    }

    
    /**
     * 
     * method validationMethod_validatemaxlen($field)
     *
     *	@var $field fieldname
     */
    function validationMethod_validatemaxlen($fieldname, $fieldvalue, $maxlen) {
#    	debug($fieldname);
#    	debug($this->parameters->_iterator);
		
    	return (strlen($fieldvalue) <= $maxlen);
    }
    
    /**
     * 
     * method validationMethod_validateminlen($field)
     *
     *	@var $field fieldname
     */
    function validationMethod_validateminlen($fieldname, $fieldvalue, $minlen) {
#    	debug($fieldname);
#    	debug($this->parameters->_iterator);
    	return (strlen($fieldvalue) >= $minlen);
    }
    
    /**
     * 
     * method validationMethod_validateselection($field)
     *
     *	@var $field fieldname
     */
    function validationMethod_validateselection($fieldname, $fieldvalue) {
//    	debug($fieldname);
//    	debug($fieldvalue);
//    	debug($this->parameters->_iterator);
    	return ($fieldvalue != 'feuserregister_invalid');
    }
    
    
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/controllers/class.tx_feuserregister_controller_frontenduserregistration.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/controllers/class.tx_feuserregister_controller_frontenduserregistration.php']);
}

?>