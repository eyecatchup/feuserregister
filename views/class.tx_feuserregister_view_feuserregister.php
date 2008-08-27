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
 * Class that implements the view for feuserregister.
 *
 * the view for the feuserregister extension
 *
 *
 * @author	Cross Content Media / e-netconsulting <dev@cross-content.com / team@e-netconsulting.de>
 * @package	TYPO3
 * @subpackage	tx_feuserregister
 */

tx_div::load('tx_lib_smartyView');
#tx_lib_smartyView::setTemplateExtension('tpl');

class tx_feuserregister_view_feuserregister extends tx_lib_smartyView {

  
	function printArray($arr) {
		return '<pre>'.print_r($arr, true).'</pre>';
	}

	function renderConfirmation($view){
		$this->_loadSmarty();
		$marker = $this->controller->model->fields;
		
		foreach ($marker as $key){
			$markerArray[$key] = $this->controller->model->get($key);			
		}
		
		$ret = $this->assignMarker($markerArray);
		# debug($markerArray);
		$out = $this->smarty->display($view.'.tmpl');
		# debug($out);
		return $out;
	}
	
	function renderConfirmationRequestMail($view, $markerArray){
		$this->_loadSmarty();
		
		
		$ret = $this->assignMarker($markerArray);
		# debug($markerArray);
		$out = $this->smarty->display($view.'.tmpl');
		# debug($out);
		return $out;
	}
	
	/**
	 * Render the Smarty template, translate and return the output as string.
	 * I overwrote the method, because at the moment it's not possible to use another smarty ext than rtp_smarty	 
	 *
	 * @param	string		name of template file without the ".tpl" suffix
	 * @return	string		typically an (x)html string
	 */
 	/**
	 * Render the Smarty template, translate and return the output as string.
	 * I overwrote the method, because at the moment it's not possible to use another smarty ext than rtp_smarty	 
	 *
	 * @param	string		name of template file without the ".tpl" suffix
	 * @return	string		typically an (x)html string
	 */
    function render($view){
        
    	$hiddenMarker = array();
    	
    	$this->_loadSmarty();
        
        
        
        $nextStep = $this->controller->getNextStep();
        $markerArray = array (
    	    'step' => $nextStep
        );
        $showPreview = $this->controller->showPreview();
        
        $labelMarker = array();
//        debug($currentStep);
//        debug($this->controller->getSteps());
        foreach ($this->controller->getSteps() as $tmpStep => $stepConfig){
        	$seperateLabels = FALSE; 
        	if ($stepConfig['seperate_label_markers'] == 1){
        		$seperateLabels = TRUE;
        	}
        	foreach ($stepConfig['fields.'] as $key => $conf){
	        	//debug($key,'key', __FILE__ , __LINE__);
	        	//debug($conf['label.'],'key', __FILE__ , __LINE__);
	        	if ($showPreview){
#	        		t3lib_div::debug('showPreview');
	        		$conf2 = $this->controller->fetchPreviewFieldConfig($stepConfig);
	        		$conf = $conf2[$key];
	        		#$conf['type'] = 'hidden';
	        	}

	        	$tmp = $this->_removeDot($key);
	        	$label = $this->controller->cObjectWrapper->cObject->stdWrap('%%%'.$tmp.'%%%', $conf['label.']);
	        	$validate_label = $this->controller->cObjectWrapper->cObject->stdWrap('%%%'.$tmp.'_validate_label%%%', $conf['validate_label.']);
	        	$dontWrap = false;
	        	if ($nextStep != $tmpStep) {
	        		$conf['type'] = 'hidden';
	        		$label = '';
	        		$validate_label = '';
	        		$dontWrap = true;
	        	} else {
	        		$nowStep = strtolower($this->_removeDot($this->controller->origKeysMapping[$tmpStep]));
	        		$savebuttonlabel = '%%%SAVE_LABEL_STEP_'.$nowStep.'%%%';
	        		$backButtonLabel = '%%%BACK_LABEL_STEP_'.$nowStep.'%%%';
//		        	debug($stepConfig);        		
	        	}
	        	$labelMarker[$tmp] = $label;
	        	$labelMarker[$tmp.'_validate_label'] = $validate_label;
	        	
	        	$value = $this->controller->parameters->get($tmp);	        	
	        	
	        	$origValue = $value;
				//debug($key,'key', __FILE__ , __LINE__);
				//debug($value,'value', __FILE__ , __LINE__);
				//debug($conf,'conf', __FILE__ , __LINE__);
	        	// Wrap around the value	        	
	        	$value = $this->controller->cObjectWrapper->cObject->stdWrap($value, $conf['value.']);
	        	if (!$showPreview){
	        		$input = $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value, 0);
	        		if (is_array($conf['validate.'])){
						if (array_key_exists('secondInputField', $conf['validate.'])) {
			        		if ($conf['validate.']['secondInputField'] == 1) {			        			
			        			$useSecondInputFieldID = TRUE;
				        		$value_validate = $this->controller->parameters->get($tmp.'_validate');	        				        			
			        			$input_validate = $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value_validate, 0, $useSecondInputFieldID);	        		
							}
						}		        			        			
	        		}
	        	} else {
	        		$input = $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value, 1);
	        		$tmpConfType = $conf['type'];
	        		$conf['type'] = 'hidden';
	        		$label = '';
	        		$input .= $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value, 0);
	        		if (is_array($conf['validate.'])){
						if (array_key_exists('secondInputField', $conf['validate.'])) {
							$conf['type'] = $tmpConfType;
							if ($conf['type'] == 'password') $conf['type'] = 'hidden';
			        		if ($conf['validate.']['secondInputField'] == 1) {
			        			$useSecondInputFieldID = TRUE;
				        		$value_validate = $this->controller->parameters->get($tmp.'_validate');	        				        			
			        			$input_validate = $this->controller->functionsObject->getTCAMarker($tmp.'_validate', $conf, $value_validate, 0, $useSecondInputFieldID);	        		
			        			if (!$seperateLabels) {
						        	$markerArray[$tmp.'_validate'] = $this->controller->cObjectWrapper->cObject->stdWrap($validate_label.$input_validate, $conf['all.']);	        		
					        	} else {
					        		$markerArray[$tmp.'_validate'] = $input_validate;
					        	}
							}
							$conf['type'] = 'hidden';
						}		        			        			
	        		}
	        	}

	        	$origInput = $input;
	        	$origInput_validate = $input_validate;
	        	
	        	// Wrap around the input form field
	        	if (!$dontWrap){
	        		$input = $this->controller->cObjectWrapper->cObject->stdWrap($input, $conf['input.']);
	        		$input_validate = $this->controller->cObjectWrapper->cObject->stdWrap($input_validate, $conf['input.']);
	        	}
	        	// Wrap around the whole marker
	        	
	        	
	        	// Extra Validierungsfelder erzeugen
	        	if (is_array($conf['validate.'])){
	        		if (array_key_exists('secondInputFieldErrormsg', $conf['validate.'])){
		        		$errmsg = $conf['validate.']['secondInputFieldErrormsg'];
		        		$errmsg = $this->controller->cObjectWrapper->cObject->stdWrap($errmsg, $conf['validate_error.']);
		        		$markerArray[$tmp.'_validate_error'] = '<div id="advice-'.$tmp.'_validate" style="display:none">'.$errmsg.'</div>';	        		
	        		}
	        		if (array_key_exists('errormsg', $conf['validate.'])){
		        		$errmsg = $conf['validate.']['errormsg'];
		        		$errmsg = $this->controller->cObjectWrapper->cObject->stdWrap($errmsg, $conf['validate_error.']);
		        		$markerArray[$tmp.'_error'] = '<div id="advice-'.$tmp.'_validate" style="display:none">'.$errmsg.'</div>';	        		
	        		}
	        		if (array_key_exists('secondInputField', $conf['validate.'])) {
//	        			debug($conf['validate.']);
	        			if ($conf['validate.']['secondInputField'] == 1) {
	        				if (!$seperateLabels) {
	        					$markerArray[$tmp.'_validate'] = $this->controller->cObjectWrapper->cObject->stdWrap($label.$input_validate, $conf['all.']);
#	        					t3lib_div::debug($markerArray);
	        				} else {
				        		$markerArray[$tmp.'_validate'] = $input_validate;
				        		$markerArray[$tmp.'_validate_label'] = $validate_label;
	        				}
						}
					}
	        	}
	        	
	        	// Normale Felder erzeugen
	        	if ($dontWrap){
	        		// dontWrap == TRUE => hidden field
	        		// seperateLabels == ? => input (label nicht rendern, da Feld nciht angeziegt wird und nur fürs spätere Abspeichern mitgeschleppt 
	        		$markerArray[$tmp] = $input;
	        	} else if (!$seperateLabels) {
	        		// dontWrap == FALSE => Feld wird angezeigt und muss mit 'all' gewrapped werden
	        		// seperateLabel == FALSE => _label marker brauchen nicht gerendert werden
	        		$markerArray[$tmp] = $this->controller->cObjectWrapper->cObject->stdWrap($label.$input, $conf['all.']);
	        	} else {
	        		// dontWrap == FALSE => Feld wird angezeigt und muss mit 'all' gewrapped werden
	        		// seperateLabels == TRUE => extra _label marker rendern
	        		$markerArray[$tmp] = $this->controller->cObjectWrapper->cObject->stdWrap($input, $conf['all.']);
	        		$markerArray[$tmp.'_label'] = $this->controller->cObjectWrapper->cObject->stdWrap($label, $conf['all.']);	        	
	        	}
	        }
		}
//		t3lib_div::debug($markerArray);
		$newUserUid = $this->controller->getNewUserUID();
		if ($newUserUid){
			$conf['type'] = 'hidden';
			$input = $this->controller->functionsObject->getTCAMarker('uid', $conf, $newUserUid);
	       	$markerArray[$tmp] = $input;
		}
#        t3lib_div::debug('markerArray:');        
#        t3lib_div::debug($markerArray);        
        $ret = $this->assignMarker($markerArray);
        $ret = $this->assignLabelMarker($markerArray, $labelMarker);
        
        $this->smarty->assign('savebutton','<input type="submit" class="feuserregsiter_button" name="feuserregister[feuserregister_submitbutton_'.$this->controller->action.']" id="feuserregister_submitbutton" value="'.$savebuttonlabel.'" />');
        $this->smarty->assign('backbutton','<input type="submit" class="feuserregsiter_button" id="feuserregister_backbutton" name="feuserregister[backbutton_'.$this->controller->action.']" value="'.$backButtonLabel.'" />');
        $this->smarty->assign('savebutton_name','feuserregister[feuserregister_submitbutton_'.$this->controller->action.']');
        $this->smarty->assign('backbutton_name','feuserregister[backbutton_'.$this->controller->action.']');
        $this->smarty->assign('stepmessage', $stepConfig['message']);
		
		$this->smarty->assign('preview', $this->_getPreviewValue());
		$this->smarty->assign('belowFormMarker', $this->controller->getBelowForm());
		
#		debug ($markerArray);
#		 debug($view);		
#		 debug($this->smarty->template_dir);		
     return $this->smarty->display($view.'.tmpl');
	 }

	function renderConfirmationResendRequest($view) {
    	$this->_loadSmarty();
        
#        debug($view);
        
        $markerArray = array (
        );
#        t3lib_div::debug($this->controller->getFieldByName('username'));
        $labelMarker = array();
#        foreach ($this->controller->getEditFieldsConfig() as $key => $conf){
        	

        $tmp = 'email';
        $label = '%%%'.$tmp.'%%%';
        $markerArray[$tmp.'_label'] = $label;
        $markerArray[$tmp.'_value'] = $this->controller->parameters->get($tmp);	        	
        
        $username = 'username';
        $label = '%%%'.$username.'%%%';
        $markerArray[$username.'_label'] = $label;
        $markerArray[$username.'_value'] = $this->controller->parameters->get($username);	        	
        
        // Wrap around the value	        	
        $input = $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value, 0);
        
        $markerArray[$tmp] = $input;

        $input = $this->controller->functionsObject->getTCAMarker($username, $conf, $value, 0);
        
        $markerArray[$username] = $input;

        $ret = $this->assignMarker($markerArray);
        $this->smarty->assign('savebutton', $hidden.'<input class="feuserregsiter_button" type="submit" value="%%%send%%%" />');
#		debug($markerArray);
#		debug($view);
        return $this->smarty->display($view.'.tmpl');
	}
	
	function renderEdit($view){
        
        
    	$hiddenMarker = array();
    	
    	$this->_loadSmarty();
        
        
        
        $markerArray = array (
    	    'editsave' => 'yes'
        );
#        $showPreview = $this->controller->showPreview();
        
        $labelMarker = array();
//        debug($currentStep);
//        debug($this->controller->getSteps());
        foreach ($this->controller->getEditFieldsConfig() as $key => $conf){
#          debug($tmpStep);
#          debug($stepConfig);
        	$seperateLabels = FALSE; 
        	if ($this->controller->getEditConfigSeperateLabelMarker() == 1){
#        	 debug('seperate');
        		$seperateLabels = TRUE;
        	}
   #     	foreach ($stepConfig['fields.'] as $key => $conf){
#	        	debug($key);
#	        	if ($showPreview){
#	        		t3lib_div::debug('showPreview');
#	        		$conf2 = $this->controller->fetchPreviewFieldConfig($stepConfig);
#	        		$conf = $conf2[$key];
	        		#$conf['type'] = 'hidden';#
#	        	}

	        	$tmp = $this->_removeDot($key);
	        	$label = $this->controller->cObjectWrapper->cObject->stdWrap('%%%'.$tmp.'%%%', $conf['label.']);
	        	$validate_label = $this->controller->cObjectWrapper->cObject->stdWrap('%%%'.$tmp.'_validate_label%%%', $conf['validate_label.']);
	        	$dontWrap = false;

        		$savebuttonlabel = '%%%SAVE_LABEL_EDIT%%%';

	        	$labelMarker[$tmp] = $label;
	        	$labelMarker[$tmp.'_validate_label'] = $validate_label;
	        	
	        	$value = $GLOBALS['TSFE']->fe_user->user[$tmp];	        	
#	        	debug($value);
	        	$origValue = $value;
//	        	t3lib_div::debug($key);
//	        	t3lib_div::debug($value);
	        	#t3lib_div::debug($conf);
	        	// Wrap around the value	        	
	        	$value = $this->controller->cObjectWrapper->cObject->stdWrap($value, $conf['value.']);
	        	$markerArray[$tmp.'_value'] = $value;
	        	if (!$showPreview){
	        		$input = $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value, 0);
	        		if (is_array($conf['validate.'])){
						if (array_key_exists('secondInputField', $conf['validate.'])) {
			        		if ($conf['validate.']['secondInputField'] == 1) {
			        			$useSecondInputFieldID = TRUE;
			        			
			        			if ($conf['validate.']['secondInputField'] == 1) {
			        			}
					        	$input_validate = $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value, 0, $useSecondInputFieldID);	        		
							}
						}		        			        			
	        		}
	        	} else {
	        		$input = $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value, 1);
	        		$tmpConfType = $conf['type'];
	        		$conf['type'] = 'hidden';
	        		$label = '';
	        		$input .= $this->controller->functionsObject->getTCAMarker($tmp, $conf, $value, 0);
	        		if (is_array($conf['validate.'])){
						if (array_key_exists('secondInputField', $conf['validate.'])) {
							$conf['type'] = $tmpConfType;
			        		if ($conf['validate.']['secondInputField'] == 1) {
					        	if (!$seperateLabels) {
						        	$markerArray[$tmp.'_validate'] = $this->controller->cObjectWrapper->cObject->stdWrap($validate_label.$input_validate, $conf['all.']);	        		
					        	} else {
					        		$markerArray[$tmp.'_validate'] = $input_validate;
					        	}
							}
							$conf['type'] = 'hidden';
						}		        			        			
	        		}
	        	}

	        	$origInput = $input;
	        	$origInput_validate = $input_validate;
	        	
	        	// Wrap around the input form field
	        	if (!$dontWrap){
	        		$input = $this->controller->cObjectWrapper->cObject->stdWrap($input, $conf['input.']);
	        		$input_validate = $this->controller->cObjectWrapper->cObject->stdWrap($input_validate, $conf['input.']);
	        	}
	        	// Wrap around the whole marker
	        	
	        	
	        	// Extra Validierungsfelder erzeugen
	        	if (is_array($conf['validate.'])){
	        		if (array_key_exists('secondInputFieldErrormsg', $conf['validate.'])){
		        		$errmsg = $conf['validate.']['secondInputFieldErrormsg'];
		        		$markerArray[$tmp.'_validate_error'] = '<div id="advice-'.$tmp.'_validate" style="display:none">'.$errmsg.'</div>';	        		
	        		}
	        		if (array_key_exists('errormsg', $conf['validate.'])){
		        		$errmsg = $conf['validate.']['errormsg'];
		        		$markerArray[$tmp.'_error'] = '<div id="advice-'.$tmp.'_validate" style="display:none">'.$errmsg.'</div>';	        		
	        		}
	        		if (array_key_exists('secondInputField', $conf['validate.'])) {
//	        			debug($conf['validate.']);
	        			if ($conf['validate.']['secondInputField'] == 1) {
	        				if (!$seperateLabels) {
	        					$markerArray[$tmp.'_validate'] = $this->controller->cObjectWrapper->cObject->stdWrap($label.$input_validate, $conf['all.']);
#	        					t3lib_div::debug($markerArray);
	        				} else {
				        		$markerArray[$tmp.'_validate'] = $input_validate;
				        		$markerArray[$tmp.'_validate_label'] = $validate_label;
	        				}
						}
					}
	        	}
	        	
	        	// Normale Felder erzeugen
	        	if ($dontWrap){
	        		// dontWrap == TRUE => hidden field
	        		// seperateLabels == ? => input (label nicht rendern, da Feld nciht angeziegt wird und nur fürs spätere Abspeichern mitgeschleppt 
	        		$markerArray[$tmp] = $input;
	        	} else if (!$seperateLabels) {
	        		// dontWrap == FALSE => Feld wird angezeigt und muss mit 'all' gewrapped werden
	        		// seperateLabel == FALSE => _label marker brauchen nicht gerendert werden
	        		$markerArray[$tmp] = $this->controller->cObjectWrapper->cObject->stdWrap($label.$input, $conf['all.']);
	        	} else {
	        		// dontWrap == FALSE => Feld wird angezeigt und muss mit 'all' gewrapped werden
	        		// seperateLabels == TRUE => extra _label marker rendern
	        		$markerArray[$tmp] = $this->controller->cObjectWrapper->cObject->stdWrap($input, $conf['all.']);
	        		$markerArray[$tmp.'_label'] = $this->controller->cObjectWrapper->cObject->stdWrap($label, $conf['all.']);	        	
	        	}
	 #       }
		}
//		t3lib_div::debug($markerArray);
#        t3lib_div::debug('markerArray:');        
#        t3lib_div::debug($markerArray);        
        $ret = $this->assignMarker($markerArray, 'edit');
        $ret = $this->assignLabelMarker($markerArray, $labelMarker);
        
        $this->smarty->assign('savebutton','<input type="submit" value="'.$savebuttonlabel.'" />');
		$this->smarty->assign('stepmessage', $stepConfig['message']);
		
		$this->smarty->assign('preview', $this->_getPreviewValue());
		$this->smarty->assign('belowFormMarker', $this->controller->getBelowForm());
		
//		debug ($markerArray);
#		 debug($view);		
#		 debug($this->smarty->template_dir);		
     return $this->smarty->display($view.'.tmpl');



	 }


 	/**
	 * Render the Smarty template, translate and return the output as string.
	 * I overwrote the method, because at the moment it's not possible to use another smarty ext than rtp_smarty	 
	 *
	 * @param	string		name of template file without the ".tpl" suffix
	 * @return	string		typically an (x)html string
	 */
    function renderMail($view){
        
    	$this->_loadSmarty();
        
        
        $markerArray = array (
        );

        foreach ($this->controller->getSteps() as $tmpStep => $stepConfig){
	      foreach ($stepConfig['fields.'] as $key => $conf){
	       	$key = $this->_removeDot($key);
	      	$value = $this->controller->parameters->get($key);
	  		  $markerArray[$key] = $value; 
	      }
		}
		// pT = processType
        $pT = $this->controller->configurations->get('config.registerProcessType');
        if ($pT == 5) {
        	$markerArray['password_input'] = $this->controller->randPassword;
        }
        $ret = $this->assignMarker($markerArray);
        return $this->smarty->display($view.'.tmpl');
	 }
	 
	 function _getPreviewValue(){
	 	$nextStep = $this->controller->getNextStep();
	 	$origKey = $this->controller->origKeysMapping[$nextStep];
	 	#t3lib_div::debug('origKey');
	 	#t3lib_div::debug($origKey);
	 	$isActuallyPreview = $this->controller->showPreview();
	 	
	 	if ($isActuallyPreview) {
	 		$retVal = 0;
	 	} else {
	 		$retVal = $this->controller->configurations->get('config.steps.'.$origKey.'preview');
	 	}
	 	$retVal = ($retVal == 1) ? 1 : 0;
	 	return $retVal;
	 }
	 
	 function _translate($key){
	 	return $this->controller->translator->translateContent($key);
	 }
	 function _removeDot($var)
	 {
    	return substr($var, 0, strlen($var)-1);
	 }
	 
	 function _loadSmarty(){
	 	
	 	if(t3lib_extMgm::isLoaded('rtp_smarty')) {
            require_once(t3lib_extMgm::extPath('rtp_smarty').'class.tx_rtpsmarty.php');
	        $this -> smarty = tx_rtpsmarty::newSmartyTemplate();
        } else {
        	return 'smarty is not available';
        }
        $this->smarty->assign($this -> getArrayCopy());
        $this->smarty->assign_by_ref('view', $this);
        $this->smarty->template_dir = $this->pathToTemplates;
        
	 }
	 
	 function assignLabelMarker($markerarray, $labelMarker){
	   foreach ($labelMarker as $name => $value){
         $this->smarty->assign($name.'_label', $value);
	   }
   	   return true;
   }

	 function assignMarker($markerarray, $mode='create'){
#	   debug($markerarray);
       $markerarray = $this->getAdditionalMarker($markerarray);
       $this->controller->hookHandler->call('getAdditionalMarker', $this, $markerarray);
       $markerarray = $this->controller->addConfirmationMarker($markerarray);
       $markerarray['form_action'] = $this->controller->getActionURL();
       if ($this->controller->getModelVariableNoErrors() !== TRUE) {
#          debug('assign noErrors');
       	$this->smarty->assign('validationerror', $this->controller->getModelVariableNoErrors());       
       }
	 	foreach ($markerarray as $name => $value){
       	 $this->smarty->assign($name, $value);
         $this->smarty->assign($name.'_isset', 1);
         if (array_key_exists($name,$this->controller->stepRequirementErrors)) {
         	$this->smarty->assign($name.'_error', $this->renderError($name));       
         }
         if (array_key_exists($name.'_validate',$this->controller->stepRequirementErrors)) {
         	$this->smarty->assign($name.'_validate_error', $this->renderError($name.'_validate'));       
         }
         if (array_key_exists('captcha_input_error',$this->controller->stepRequirementErrors)) {
         	$this->smarty->assign('captcha_input_error', $this->renderError('captcha_input_error'));       
         }
	 	}
#	 	debug($this->renderError($name.'_validate'));
       $step = $markerarray['step'];
       $this->smarty->assign('step_'.$this->_removeDot(($this->controller->stepKey)), 1);
       $this->smarty->assign('hiddenFields', $this->_getHiddenInputFields($mode, $step, $markerarray));
   	   return true;
   }
   
   function renderError($name) {
   	$out = '';
   	$error = $this->controller->stepRequirementErrors[$name];
   	$confirm = FALSE;
   	if (strpos($name, '_validate')) {
   		$confirm = TRUE;
   		$name = str_replace('_validate', '', $name);
   	}
   	$error_lang = $this->controller->translator->translate('%%%'.$error.'%%%');
 	$nextStep = $this->controller->getNextStep();
 	$origKey = $this->controller->origKeysMapping[$nextStep];
   	$conf = $this->controller->configurations->get('config.steps.'.$origKey.'fields.'.$name.'.');
#   	t3lib_div::debug('renderError');
#  	t3lib_div::debug('config.steps.'.$origKey.'fields.'.$name.'.');
#	debug($conf);
	if(is_array($conf['validate.'])) {
		if ($confirm) {
			if (is_array($conf['validate.']['err_confirm_message.'])) {
				$error_lang = $this->controller->cObjectWrapper->cObject->stdWrap($error_lang, $conf['validate.']['err_confirm_message.']);
			}
		}
		else {
			if (is_array($conf['validate.']['err_message.'])) {
				$error_lang = $this->controller->cObjectWrapper->cObject->stdWrap($error_lang, $conf['validate.']['err_message.']);
			}
		}
	}
   	return $this->controller->cObjectWrapper->cObject->stdWrap($error_lang, $conf['error.']);
   }
   
   function _getHiddenInputFields($mode = 'create', $step = 0, $markerarray = array()) {
   	$preview = $this->_getPreviewValue();
   	if ($mode == 'edit'){
     	$ret = '
        <input type="hidden" name="feuserregister[action]" value="'.$mode.'"/>
     	';
      if (isset($markerarray['editsave'])) {
       	$ret .= '
          <input type="hidden" name="feuserregister[saveedit]" value="'.$markerarray['editsave'].'"/>
       	';
      }
      return $ret;
    }
    
    $previewReturn = '';
    $get = 'preview';
    //FIXME: besser: kontrollieren ob der vorhergehende step preview=1 hat
    if (!$preview && !$this->controller->isFirstStep()) {    	
    	$previewReturn = '<input type="hidden" name="feuserregister[returnFromPreview]" value="1"/>';
    }
#    debug($previewReturn);
   	return $previewReturn.'
      <input type="hidden" name="feuserregister[action]" value="'.$mode.'"/>
      <input type="hidden" name="feuserregister[step]" value="'.$step.'"/>
      <input type="hidden" name="feuserregister[preview]" value="'.$preview.'"/>
   	';
   }
   
   
   function getAdditionalMarker($markerarray) {
   	foreach ($this->controller->getAdditionalMarker() as $key => $value) {
   		$markerarray[$key] = $value;
   	}
   	$markerarray['confirm_link_href_user'] = $this->controller->getConfirmLinkHref('user');
   	$markerarray['confirm_link_href_admin'] = $this->controller->getConfirmLinkHref('admin');
   	return $markerarray;
   }
   
   
   
   	/***************************
	 *
	 * Stylesheet, CSS
	 *
	 **************************/


	/**
	 * Returns a class-name prefixed with $this->prefixId and with all underscores substituted to dashes (-)
	 *
	 * @param	string		The class name (or the END of it since it will be prefixed by $this->prefixId.'-')
	 * @return	string		The combined class name (with the correct prefix)
	 */
	function pi_getClassName($class)	{
		return str_replace('_','-',$this->prefixId).($this->prefixId?'-':'').$class;
	}

	/**
	 * Returns the class-attribute with the correctly prefixed classname
	 * Using pi_getClassName()
	 *
	 * @param	string		The class name(s) (suffix) - separate multiple classes with commas
	 * @param	string		Additional class names which should not be prefixed - separate multiple classes with commas
	 * @return	string		A "class" attribute with value and a single space char before it.
	 * @see pi_getClassName()
	 */
	function pi_classParam($class, $addClasses='')	{
		$output = '';
		foreach (t3lib_div::trimExplode(',',$class) as $v)	{
			$output.= ' '.$this->pi_getClassName($v);
		}
		foreach (t3lib_div::trimExplode(',',$addClasses) as $v)	{
			$output.= ' '.$v;
		}
		return ' class="'.trim($output).'"';
	}

	/**
	 * Sets CSS style-data for the $class-suffix (prefixed by pi_getClassName())
	 *
	 * @param	string		$class: Class suffix, see pi_getClassName
	 * @param	string		$data: CSS data
	 * @param	string		If $selector is set to any CSS selector, eg 'P' or 'H1' or 'TABLE' then the style $data will regard those HTML-elements only
	 * @return	void
	 * @deprecated		I think this function should not be used (and probably isn't used anywhere). It was a part of a concept which was left behind quite quickly.
	 * @private
	 */
	function pi_setClassStyle($class,$data,$selector='')	{
		$GLOBALS['TSFE']->setCSS($this->pi_getClassName($class).($selector?' '.$selector:''),'.'.$this->pi_getClassName($class).($selector?' '.$selector:'').' {'.$data.'}');
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/views/class.tx_feuserregister_view_feuserregister.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/views/class.tx_feuserregister_view_feuserregister.php']);
}

?>