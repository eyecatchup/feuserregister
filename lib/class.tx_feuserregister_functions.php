<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2007 Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca)>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Part of the feuserregister (Frontend User Registration) extension.
 *
 * functions
 *
 * $Id: class.tx_feuserregister_functions.php 7036 2007-10-17 20:46:30Z crosscontent $
 *
 * @author Sebastian Boettger <dev@cross-content.com>
 *
 * @package TYPO3
 * @subpackage feuserregister
 *
 *
 */
 

class tx_feuserregister_functions {
	var $controller;
  	var $pibase;
	var $TCA = array();
	var $sys_language_content;
	var $cObj;
  	
  	function tx_feuserregister_functions() {
  		
  	}
	function init($controller)	{
		global $TSFE, $TCA, $TYPO3_CONF_VARS;
		$this->controller = &$controller;
		$this->pibase = &$controller->cObjectWrapper; // Fuer einfaches Copy Paste aus sr_feuser_register
		$this->sys_language_content = $pibase->sys_language_content;
		$this->cObj = $this->pibase->cObject;
		// get the table definition
		//debug($this->cObj,'cObj', __FILE__ , __LINE__);
		$TSFE->includeTCA();
		// debug($TCA,'TCA', __FILE__ , __LINE__);
		$this->TCA = $TCA['fe_users'];
#		if ($TYPO3_CONF_VARS['EXTCONF'][$extKey]['uploadFolder'])	{
#			$this->TCA[$this->controlData->getTable()]['columns']['image']['config']['uploadfolder'] = $TYPO3_CONF_VARS['EXTCONF'][$extKey]['uploadFolder'];
#		}
	}
	
	/**
	 * Returns a class-name prefixed with $this->controller->getDesignator() and with all underscores substituted to dashes (-)
	 *
	 * @param	string		The class name (or the END of it since it will be prefixed by $this->controller->getDesignator().'-')
	 * @return	string		The combined class name (with the correct prefix)
	 */	
	function getClassName($class){
		return str_replace('_','-',$this->controller->getDesignator()).($this->controller->getDesignator()?'-':'').$class;		
	}
	
	function &getTCA()	{
		return $this->TCA;
	}
	/**
	* Adds the fields coming from other tables via MM tables
	*
	* @param array  $dataArray: the record array
	* @return array  the modified data array
	*/
	function modifyTcaMMfields($dataArray, &$modArray) {
		global $TYPO3_DB;
		$rcArray = $dataArray;
		foreach ($this->TCA['columns'] as $colName => $colSettings) {
			$colConfig = $colSettings['config'];
			// Configure preview based on input type
			switch ($colConfig['type']) {
				case 'select':
					if ($colConfig['MM'] && $colConfig['foreign_table']) {
						$where = 'uid_local = '.$dataArray['uid'];
						$res = $TYPO3_DB->exec_SELECTquery(
							'uid_foreign',
							$colConfig['MM'],
							$where
						);
						$valueArray = array();
						while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
							$valueArray[] = $row['uid_foreign'];
						}
						$rcArray[$colName] = implode(',', $valueArray);
						$modArray[$colName] = $rcArray[$colName]; 
					}
					break;
			}
		}
		return $rcArray;
	}


	/**
	* Modifies the incoming data row
	* Adds checkboxes which have been unset. This means that no field will be present for them.
	* Fetches the former values of select boxes
	*
	* @param array  $dataArray: the input data array will be changed
	* @return void
	*/
	function modifyRow(&$dataArray)	{
		global $TYPO3_DB;
		$fieldsList = array_keys($dataArray);
		foreach ($this->TCA['columns'] as $colName => $colSettings) {
			$colConfig = $colSettings['config'];
			switch ($colConfig['type'])	{
				case 'select':
					if (in_array($colName, $fieldsList) && $colConfig['MM']) {
						if (!$dataArray[$colName]) {
							$dataArray[$colName] = '';
						} else {
							$valuesArray = array();
							$res = $TYPO3_DB->exec_SELECTquery(
								'uid_local,uid_foreign,sorting',
								$colConfig['MM'],
								'uid_local='.intval($dataArray['uid']),
								'',
								'sorting');
							while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
								$valuesArray[] = $row['uid_foreign'];
							}
							$dataArray[$colName] = implode(',', $valuesArray);
						}
					}
					break;
				case 'check':
					if (is_array($colConfig['type']['items'])) {
						$value = $dataArray[$colName];
						if(is_array($value)) {
							$dataArray[$colName] = 0;
							foreach ($value AS $dec) {  // Combine values to one hexidecimal number
								$dataArray[$colName] |= (1 << $dec);
							}
						}
					} else if (isset($dataArray[$colName]) && $dataArray[$colName]!='0') {
						$dataArray[$colName] = '1';
					} else {
						$dataArray[$colName] = '0';
					}
					break;
				default:
					// nothing
					break;
			}
		}
	}

	/**
	 * Returns the class-attribute with the correctly prefixed classname
	 * Using pi_getClassName()
	 *
	 * @param	string		The class name(s) (suffix) - separate multiple classes with commas
	 * @param	string		Additional class names which should not be prefixed - separate multiple classes with commas
	 * @return	string		A "class" attribute with value and a single space char before it.
	 * @see getClassName()
	 */	
	function classParam($class, $addClasses='')	{
		$output = '';
		foreach (t3lib_div::trimExplode(',',$class) as $v)	{
			$output.= ' '.$this->getClassName($v);
		}
		foreach (t3lib_div::trimExplode(',',$addClasses) as $v)	{
			$output.= ' '.$v;
		}
		return ' class="'.trim($output).'"';
	}
	

	/**
	* Returns a form element from the Table Configuration Array to a marker array
	*
	* @param array  $colName: the requested column
	* @param array  $conf: ts config
	* @param array  $value: a specific value
	* @return void
	*/

	function getTcaMarker($colName, $conf, $value='', $previewMode = 0, $useSecondInputFieldID = FALSE) {
		global $TYPO3_DB, $TCA, $TSFE;
		$charset = $TSFE->renderCharset;
		$designator = $this->controller->getDesignator();
		$fieldId = $this->controller->getFieldId($colName);
		//debug($colName,'', __FILE__ , __LINE__);
		//debug($this->TCA['columns'][$colName],'', __FILE__ , __LINE__);
			$colSettings = $this->TCA['columns'][$colName];
				if ($conf['type'] == 'hidden') {
					$colSettings['config']['type'] = 'hidden';
				} else if ($conf['type'] == 'password') {
					$colSettings['config']['type'] = 'password';
				} else if ($conf['type'] == 'captcha') {
					################################
					# marker for captcha extension
					################################
					if (t3lib_extMgm::isLoaded('captcha')){
						return '<img src="'.t3lib_extMgm::siteRelPath('captcha').'captcha/captcha.php" alt="" />';
					}					
				} else if ($colName == 'captcha_input') {
					$colSettings['config']['type'] = $conf['type'] ? $conf['type'] : 'input';					
				}
#				debug($colName);
				if (is_array($conf['tca.']))
				foreach ($conf['tca.'] as $key => $value) {
          $colSettings['config'][$key] = $value;
        }
        
				$colConfig = $colSettings['config'];
				if ($value != '') $colConfig['default'] = $value;
				$colContent = '';
				//debug($previewMode,'previewMode', __FILE__ , __LINE__);
				//debug($value,'', __FILE__ , __LINE__);
				//debug($colConfig,'', __FILE__ , __LINE__);

				if ($colConfig['type'] == 'select') {
					//debug($conf,'conf', __FILE__ , __LINE__);
				}
				if ($conf['additionalAttributes'] == '') {
					if ($conf['addParams'] != '') {
						$conf['additionalAttributes'] = $conf['addParams'];
					}
				}
				
				if ($conf['additionalClasses'] == '' || (is_array($conf['validate.']) )){					
					if (is_array($conf['validate.'])) {
						if ($conf['validate.']['validationMethod']) {
							$conf['additionalClasses'] = str_replace(',', ' ', $conf['validate.']['validationMethod']);
						}
						$require = ' required ';
						if ($conf['validate.']['notRequired']){
              $require = '';
            }
						if ($conf['validate.']['secondInputField']){
							if ($useSecondInputFieldID) {
								$conf['additionalClasses'] .= ' '.$require.' validate-'.$fieldId.'-confirm';
							} else {
								$conf['additionalClasses'] .= ' '.$require.' validate-'.$fieldId;
							}
						}
					}
				} else {
					if (is_array($conf['validate.'])) {
						if ($conf['validate.']['validationMethod']) {
							$conf['additionalClasses'] = str_replace(',', ' ', $conf['validate.']['validationMethod']);
						}
					}
				}
				$additionalClasses = ' class="'.$conf['additionalClasses'].'" ';
				
				if ($useSecondInputFieldID) {
					$fieldId = ' id="'.$this->controller->getFieldId($colName, '_validate').'"';					
				} else {
					$fieldId = ' id="'.$fieldId.'"';	
				}
				
				if ($previewMode || $viewOnly) {
						// Configure preview based on input type
						switch ($colConfig['type']) {
							case 'input':
							case 'text':
								$colContent = nl2br(htmlspecialchars($value,ENT_QUOTES,$charset));
								//debug($colContent,'colContent', __FILE__ , __LINE__);
								break;
							case 'check':
								if (is_array($colConfig['items'])) {
									$colContent = '<ul class="tx-feuserregister-multiple-checked-values">';
									foreach ($colConfig['items'] as $key => $value) {
										$label = htmlspecialchars($this->controller->translator->translateContent($colConfig['items'][$key][0]),ENT_QUOTES,$charset);
										$checked = ($value & (1 << $key));
										$colContent .= ($checked ? '<li>' . $label . '</li>' : '');
									}
									$colContent .= '</ul>';
								} else {
									$colContent = $value?htmlspecialchars($this->controller->translator->translate('%%%yes%%%'),ENT_QUOTES,$charset):htmlspecialchars($this->controller->translator->translateContent('%%%no%%%'),ENT_QUOTES,$charset);
								}
								break;
							case 'radio':
								if ($value != '') {
									$colContent = htmlspecialchars($this->controller->translator->getLLFromString($colConfig['items'][$value][0]),ENT_QUOTES,$charset);
								}
								break;
							case 'select':
								if ($value != '') {
									$valuesArray = is_array($value) ? $value : explode(',',$value);
									$textSchema = 'fe_users.'.$colName.'.I.';
									$itemArray = $this->controller->translator->getItemsLL($textSchema, true);
									$bUseTCA = false;
									if (!count ($itemArray))	{
										$itemArray = $colConfig['items'];
										$bUseTCA = true;
									}
									if (is_array($itemArray)) {
										$itemKeyArray = $this->getItemKeyArray($itemArray);
										//debug(array($itemKeyArray,$valuesArray),'ELECT itemArray', __FILE__ , __LINE__);
										$stdWrap = array();
										if (is_array($this->conf['select.']) && is_array($this->conf['select.'][$activity.'.']) && is_array($this->conf['select.'][$activity.'.'][$colName.'.']))	{
											$stdWrap = $this->conf['select.'][$activity.'.'][$colName.'.'];
										} else {
											$stdWrap['wrap'] = '|<br />';
										}
										for ($i = 0; $i < count ($valuesArray); $i++) {
											#$text = $this->controller->translator->translateContent($itemKeyArray[$valuesArray[$i]][0]);
											$text = $itemKeyArray[$valuesArray[$i]][0];
											$text = htmlspecialchars($text,ENT_QUOTES,$charset);
											$colContent .= $this->controller->cObjectWrapper->cObject->stdWrap($text,$stdWrap);
										}
									}
									if ($colConfig['foreign_table']) {
										t3lib_div::loadTCA($colConfig['foreign_table']);
										$reservedValues = array();
										if ($theTable == 'fe_users' && $colName == 'usergroup') {
											$reservedValues = array_merge(t3lib_div::trimExplode(',', $this->conf['create.']['overrideValues.']['usergroup'],1), t3lib_div::trimExplode(',', $this->conf['setfixed.']['APPROVE.']['usergroup'],1), t3lib_div::trimExplode(',', $this->conf['setfixed.']['ACCEPT.']['usergroup'],1));
										}
										$valuesArray = array_diff($valuesArray, $reservedValues);
										reset($valuesArray);
										$firstValue = current($valuesArray);
										if (!empty($firstValue) || count ($valuesArray) > 1) {
											$titleField = $TCA[$colConfig['foreign_table']]['ctrl']['label'];
											$where = 'uid IN ('.implode(',', $valuesArray).')';
											$res = $TYPO3_DB->exec_SELECTquery(
												'*',
												$colConfig['foreign_table'],
												$where
												);
											$i = 0;
											while ($row2 = $TYPO3_DB->sql_fetch_assoc($res)) {
												if ($theTable == 'fe_users' && $colName == 'usergroup') {
													$row2 = $this->getUsergroupOverlay($row2);
												} elseif ($localizedRow = $TSFE->sys_page->getRecordOverlay($colConfig['foreign_table'], $row2, $this->sys_language_content)) {
													$row2 = $localizedRow;
												}
												$colContent .= ($i++ ? '<br />': '') . htmlspecialchars($row2[$titleField],ENT_QUOTES,$charset);
											}
										}
									}
								}
								break;
							case 'hidden';
								break;
							default:
								// unsupported input type
								$colContent .= $colConfig['type'].':'.htmlspecialchars($this->controller->translator->translateContent('unsupported'),ENT_QUOTES,$charset);
						}
					} else {
						// Configure inputs based on TCA type
						//debug(array($colConfig['type'],$colName,$fieldId),'TCA type, colName, fieldId', __FILE__ , __LINE__);
						
						switch ($colConfig['type']) {
							case 'hidden':
								if (is_array($value)) {
									$valuesArray = $value;
									foreach ($valuesArray as $value) {
										$colContent = '<input type="hidden" name="'.$designator.'['.$colName.'][]"'.
											' size="'.($colConfig['size']?$colConfig['size']:30).'"';
										if ($colConfig['max']) {
											$colContent .= ' maxlength="'.$colConfig['max'].'"';
										}
										if ($colConfig['default'] && $value == '') {
											$label = $this->controller->translator->translateContent($colConfig['default']);
											$label = htmlspecialchars($label,ENT_QUOTES,$charset);
											$colContent .= ' value="'.$label.'"';
										} else if ($value != ''){
											$colContent .= ' value="'.$value.'"';
										}
										$colContent .= $fieldId.' />';
									}
								} else {
									$colContent = '<input type="hidden" name="'.$designator.'['.$colName.']"'.
										' size="'.($colConfig['size']?$colConfig['size']:30).'"';
									if ($colConfig['max']) {
										$colContent .= ' maxlength="'.$colConfig['max'].'"';
									}
									if ($colConfig['default'] && $value == '') {
										$label = $this->controller->translator->translateContent($colConfig['default']);
										$label = htmlspecialchars($label,ENT_QUOTES,$charset);
										$colContent .= ' value="'.$label.'"';
									} else if ($value != ''){
										$colContent .= ' value="'.$value.'"';
									}
									$colContent .= $fieldId.' />';
								}
								//debug($colContent,'colContent', __FILE__ , __LINE__);
								break;
							case 'password':
								$zusatz = '';
								if ($useSecondInputFieldID) {
									$zusatz = '_validate';
								}
								$colContent = '<input type="password" name="'.$designator.'['.$colName.$zusatz.']"'.
									' size="'.($colConfig['size']?$colConfig['size']:30).'"';
								if ($colConfig['max']) {
									$colContent .= ' maxlength="'.$colConfig['max'].'"';
								}
								//debug($colConfig,'colConfig', __FILE__ , __LINE__);
								if ($colConfig['default']) {
									$label = $this->controller->translator->translate($colConfig['default']);
									$label = htmlspecialchars($label,ENT_QUOTES,$charset);
									$colContent .= ' value="'.$label.'"';									
								}
								if (!$conf['disableTitleTag']) {
									$colContent .= ' title="%%%' . $this->cObj->caseshift($colName,'lower') . $zusatz . '_tooltip' . (($cmd == 'invite')?'_invitation':'') . '%%%" ';
								}							
								$colContent .= $conf['additionalAttributes'];
								$colContent .= $additionalClasses;
								$colContent .= $fieldId . ' />';
								//debug($colContent,'colContent', __FILE__ , __LINE__);
								break;
							case 'group':
								$zusatz = '';
								if ($colConfig['internal_type'] == 'file'){
  								$colContent = '<input type="file" name="'.$designator.'['.$colName.$zusatz.']"';
  								if ($colConfig['default'] && $value == '') {
  									$label = $this->controller->translator->translateContent($colConfig['default']);
  									$label = htmlspecialchars($label,ENT_QUOTES,$charset);
  									$colContent .= ' value="'.$label.'"';
  								}
								if (!$conf['disableTitleTag']) {
									$colContent .= ' title="%%%' . $this->cObj->caseshift($colName,'lower') . '_tooltip' . (($cmd == 'invite')?'_invitation':'') . '%%%" ';
								}  								
  								$colContent .= $conf['additionalAttributes'];
  								$colContent .= $additionalClasses;
  								$colContent .= $fieldId . ' />';								  
                				}
								//debug($colContent,'colContent', __FILE__ , __LINE__);
								break;
							case 'input':
								//debug($conf,'conf', __FILE__ , __LINE__);
#								debug($colName);
								$zusatz = '';
								if ($useSecondInputFieldID) {
									$zusatz = '_validate';
								}
								$colContent = '<input name="'.$designator.'['.$colName.$zusatz.']"'.
									' size="'.($colConfig['size']?$colConfig['size']:30).'"';
#								debug(array(1));
#								debug($colContent);
#								debug(array(1));
								if ($colConfig['max']) {
									$colContent .= ' maxlength="'.$colConfig['max'].'"';
								}
								if (!$conf['disableTitleTag']) {
									$colContent .= ' title="%%%' . $this->cObj->caseshift($colName,'lower') . $zusatz . '_tooltip' . (($cmd == 'invite')?'_invitation':'') . '%%%" ';
								}
								
								if ($colConfig['default']) {
									$label = $this->controller->translator->translate($colConfig['default']);
									$label = htmlspecialchars($label,ENT_QUOTES,$charset);
									$colContent .= ' value="'.$label.'"';
								}
								$colContent .= $conf['additionalAttributes'];
								$colContent .= $additionalClasses;
								$colContent .= $fieldId . ' />';
#								debug($colContent);
								//debug($colContent,'colContent', __FILE__ , __LINE__);
								break;
							case 'text':
								//debug($colName,'colName', __FILE__ , __LINE__);
								$label = $this->controller->translator->translateContent($colConfig['default']);
								$label = htmlspecialchars($label,ENT_QUOTES,$charset);
								$colContent = '<textarea id="'. $this->getClassName($colName) . '" name="'.$designator.'['.$colName.']"';
								if (!$conf['disableTitleTag']) $colContent .= ' title="%%%' . $this->cObj->caseshift($colName,'lower') . '_tooltip' . (($cmd == 'invite')?'_invitation':'') . '%%%"';
								$colContent .= ' cols="'.($colConfig['cols']?$colConfig['cols']:30).'"';
								$colContent .= ' rows="'.($colConfig['rows']?$colConfig['rows']:5).'" ';
								$colContent .= $conf['additionalAttributes'];
								$colContent .= $additionalClasses;
								$colContent .= $fieldId.' >';
								if ($colConfig['default']) {
									$label = $this->controller->translator->translate($colConfig['default']);
									$label = htmlspecialchars($label,ENT_QUOTES,$charset);
									$colContent .= $label;
								}
								$colContent .= '</textarea>';
								//debug($colContent,'colContent', __FILE__ , __LINE__);
								break;
							case 'check':
								$label = $this->controller->translator->translateContent($colName . '_tooltip');
								$label = htmlspecialchars($label,ENT_QUOTES,$charset);
								$fieldIdValidate = $this->controller->getFieldId($colName);
								if (is_array($colConfig['items'])) {
									$uidText = $this->getClassName($colName).'-'.$mrow['uid'];
									$colContent  = '<ul id="'. $uidText . ' " class="tx-feuserregister-multiple-checkboxes">';
									$lastCount = count($colConfig['items']);
									$i = 0;
									foreach ($colConfig['items'] as $key => $value) {
										$i++;
										if ($this->controlData->getSubmit() || $cmd=='edit')	{
											$startVal = $mrow[$colName];
										} else {
											$startVal = $colConfig['default'];
										}
										$checked = ($startVal & (1 << $key))?' checked="checked"':'';
										$label = $this->controller->translator->translateContent($colConfig['items'][$key][0]);
										$label = htmlspecialchars($label,ENT_QUOTES,$charset);
										$fieldIdValidate = $this->controller->getFieldId($colName, $uidText);
										if ($conf['additionalClasses']) {
											$classParam = ' class="'.$conf['additionalClasses'].'" ';
										} 
										if ($lastCount == $i) {
											$classParam = $additionalClasses;
										}
										
										$colContent .= '<li><input type="checkbox"' . $classParam . ' id="' . $fieldIdValidate .  ' " name="'.$designator.'['.$colName.'][]" value="'.$key.'"'.$checked.' '.$conf['additionalAttributes'].' /><label for="' . $uidText . '-' . $key .  '">' . $label . '</label></li>';
									}
									$colContent .= '</ul>';
								} else {
									$colContent = '<input type="checkbox"' . $additionalClasses . ' id="'. $fieldIdValidate . '" name="'.$designator.'['.$colName.']" title="'.$label.'"' . ($mrow[$colName]?' checked="checked"':'') . ' />';
								}
								break;
							case 'radio':
								$lastCount = count($colConfig['items']);
								$i = 0;
								for ($i = 0; $i < count ($colConfig['items']); ++$i) {
									$label = $this->controller->translator->translateContent($colConfig['items'][$i][0]);
									$label = htmlspecialchars($label,ENT_QUOTES,$charset);
									if ($conf['additionalClasses']) {
										$classParam = ' class="'.$conf['additionalClasses'].'" ';
									}
									if ($lastCount == $i) {
										$classParam = $additionalClasses;
									}
									$colContent .= '<input type="radio"' . $classParam . ' id="'. $this->getClassName($colName) . '" name="'.$designator.'['.$colName.']"'.
											' value="'.$i.'" '.($i==0 ? ' checked="checked"' : '').' />' .
											'<label for="' . $this->getClassName($colName) . '-' . $i . '">' . $label . '</label>';
								}
								break;
							case 'select':
								$fieldIdValidate = $this->controller->getFieldId($colName);							
								$colContent ='';
								if ('' != $value) $valuesArray = explode(',', $value);
								//debug($value,'TCA :: select :: value', __FILE__ , __LINE__);
								//debug($valueArray,'TCA :: select :: valueArray', __FILE__ , __LINE__);
                				#is_array($mrow[$colName]) ? $mrow[$colName] : explode(',',$mrow[$colName]);
								if (!$valuesArray[0] && $colConfig['default']) {
									$valuesArray[] = explode(',', $colConfig['defaultUIDs']);									
								}
								if (!is_array($valuesArray)) $valuesArray = array();
								//debug($valueArray,'TCA :: select :: valueArray', __FILE__ , __LINE__);
								//debug($colConfig,'TCA :: select :: colConfig', __FILE__ , __LINE__);
								if ($colConfig['maxitems'] > 1) {
									$multiple = '[]" multiple="multiple';
								} else {
									$multiple = '';
								}
								
								$validUIDs = '';
								$defaultUIDs = array();
								$inValidUIDs = '';
								//debug($colConfig,'TCA :: select :: colConfig', __FILE__ , __LINE__);
								if ($colConfig['validUIDs']) {
									$validUIDs = explode(',', $colConfig['validUIDs']);
								}

								if ($colConfig['inValidUIDs']) {
									$inValidUIDs = explode(',', $colConfig['inValidUIDs']);
								}
								
								if ($colConfig['renderMode'] == 'checkbox' && $this->conf['templateStyle'] == 'css-styled')	{
									$colContent .='
											<input id="'. $fieldIdValidate . ' " name="'.$designator.'['.$colName.']" value="" type="hidden" />';
									$colContent .='<dl class="' . $this->getClassName('multiple-checkboxes') . '"';
									if (!$conf['disableTitleTag'])  $colContent .= 'title="%%%' . $this->cObj->caseshift($colName,'lower') . '_tooltip' . (($cmd == 'invite')?'_invitation':'') . '%%%"';
									$colContent .= '>';
								} else {
									$colContent .= '<select '.$additionalClasses.'id="'. $fieldIdValidate . '" name="'.$designator.'['.$colName.']' . $multiple . '"';
									if (!$conf['disableTitleTag'])  $colContent .= ' title="%%%' . $this->controller->cObjectWrapper->cObject->caseshift($colName,'lower') . '_tooltip' . (($cmd == 'invite')?'_invitation':'') . '%%%"';
									$colContent .= '>';
									
								}
								//debug($colConfig,'TCA :: select :: colConfig', __FILE__ , __LINE__);
								$textSchema = 'fe_users.'.$colName.'.I.';
								$itemArray = $this->controller->translator->getItemsLL($textSchema, true);
								if (!count($itemArray) && is_array($validUIDs)){
									$tmp = array();
									foreach ($itemArray as $k => $value) {
										if (in_array($k, $validUIDs)) {
											$tmp[$k] = $value;
										}
									}
									$itemArray = $tmp;
								}
								if (count($itemArray) && is_array($inValidUIDs)){
									$tmp = array();
									foreach ($itemArray as $k => $value) {
										if (!in_array($k, $inValidUIDs)) {
											$tmp[$k] = $value;
										}
									}
									$itemArray = $tmp;
								}
								//debug($itemArray,'TCA :: select :: itemArray', __FILE__ , __LINE__);
								$bUseTCA = false;
								if (!count ($itemArray))	{
									$itemArray = $colConfig['items'];
									$bUseTCA = true;
								}
								if (is_array($itemArray)) {
									$itemArray = $this->getItemKeyArray($itemArray);
									$i = 0;
									if ($bUseTCA)	{
										$deftext = $itemArray[$i][0];
										$deftext = substr($deftext, 0, strlen($deftext) - 2);
									}
									$colContent .= '<option value="feuserregister_invalid">%%%'.$this->cObj->caseshift($colName,'lower').'_select_first%%%</option>';
									
									$i = 0;
									foreach ($itemArray as $k => $item)	{
										$fieldIdValidate = $this->controller->getFieldId($colName, $i);
										$label = $item[0];
										$label = htmlspecialchars($label,ENT_QUOTES,$charset);
										if ($colConfig['renderMode'] == 'checkbox' && $this->conf['templateStyle'] == 'css-styled')	{
											$colContent .= '<dt><input class="' . $this->getClassName('checkbox') . '" id="'. $fieldIdValidate .'" name="'.$designator.'['.$colName.']['.$k.']" value="'.$k.'" type="checkbox"  ' . (in_array($k, $valuesArray) ? ' checked="checked"' : '') . ' /></dt>
													<dd><label for="'. $this->getClassName($colName) . '-' . $i .'">'.$label.'</label></dd>';
										} else {
              											
											$colContent .= '<option value="'.$k. '" ' . (in_array($k, $valuesArray) ? 'selected' : '') . '>' . $label.'</option>';
										}
										$i++;
									}
								}
								if ($colConfig['foreign_table']) {
									t3lib_div::loadTCA($colConfig['foreign_table']);
									$titleField = $TCA[$colConfig['foreign_table']]['ctrl']['label'];
									if ($theTable == 'fe_users' && $colName == 'usergroup') {
										$reservedValues = array_merge(t3lib_div::trimExplode(',', $this->conf['create.']['overrideValues.']['usergroup'],1), t3lib_div::trimExplode(',', $this->conf['setfixed.']['APPROVE.']['usergroup'],1), t3lib_div::trimExplode(',', $this->conf['setfixed.']['ACCEPT.']['usergroup'],1));
										$selectedValue = false;
									}
									$whereClause = ($theTable == 'fe_users' && $colName == 'usergroup') ? ' pid='.intval($this->controlData->getPid()).' ' : ' 1=1';
									if ($TCA[$colConfig['foreign_table']] && $TCA[$colConfig['foreign_table']]['ctrl']['languageField'] && $TCA[$colConfig['foreign_table']]['ctrl']['transOrigPointerField']) {
										$whereClause .= ' AND '.$TCA[$colConfig['foreign_table']]['ctrl']['transOrigPointerField'].'=0';
									}
									if ($colName == 'module_sys_dmail_category' && $colConfig['foreign_table'] == 'sys_dmail_category' && $this->conf['module_sys_dmail_category_PIDLIST']) {
										$whereClause .= ' AND sys_dmail_category.pid IN (' . $TYPO3_DB->fullQuoteStr($this->conf['module_sys_dmail_category_PIDLIST'], 'sys_dmail_category') . ')';
									}
									$whereClause .= $this->controller->cObjectWrapper->cObject->enableFields($colConfig['foreign_table']);
									$res = $TYPO3_DB->exec_SELECTquery('*', $colConfig['foreign_table'], $whereClause, '', $TCA[$colConfig['foreign_table']]['ctrl']['sortby']);
									if (true || !in_array($colName, $this->controlData->getRequiredArray())) {
										if ($colConfig['renderMode'] == 'checkbox' || $colContent)	{
											// nothing
										} else {
											$colContent .= '<option value="" ' . ($valuesArray[0] ? '' : 'selected="selected"') . '></option>';
										}
									}
								//debug($conf,'conf', __FILE__ , __LINE__);
                  $invalidUIDs = array();
                  if (isset($conf['invalidUIDs'])) {
                    $invalidUIDs = explode(',',$conf['invalidUIDs']);
                  }
								//debug($invalidUIDs,'TCA :: select :: invalidUIDs', __FILE__ , __LINE__);
									$colContent .= '<option value="feuserregister_invalid">%%%'.$this->cObj->caseshift($colName,'lower').'_select_first%%%</option>';
									while ($row2 = $TYPO3_DB->sql_fetch_assoc($res)) {
								//debug($row2['uid'],'row2 uid', __FILE__ , __LINE__);
									  if (in_array($row2['uid'], $invalidUIDs)) continue;
										if ($designator == 'fe_users' && $colName == 'usergroup') {
											if (!in_array($row2['uid'], $reservedValues)) {
												$row2 = $this->getUsergroupOverlay($row2);
												$titleText = htmlspecialchars($row2[$titleField],ENT_QUOTES,$charset);
												$selected = (in_array($row2['uid'], $valuesArray) ? 'selected="selected"' : '');
												if(!$this->conf['allowMultipleUserGroupSelection'] && $selectedValue) {
													$selected = '';
												}
												$selectedValue = $selected ? true: $selectedValue;
												if ($colConfig['renderMode'] == 'checkbox' && $this->conf['templateStyle'] == 'css-styled')	{
													$colContent .= '<dt><input  class="' . $this->getClassName('checkbox') . '" id="'. $this->getClassName($colName) . '-' . $row2['uid'] .'" name="'.$designator.'['.$colName.']['.$row2['uid'].'"]" value="'.$row2['uid'].'" type="checkbox"' . $selected ? ' checked="checked"':'' . ' /></dt>
													<dd><label for="'. $this->getClassName($colName) . '-' . $row2['uid'] .'">'.$titleText.'</label></dd>';
												} else {
													$colContent .= '<option value="'.$row2['uid'].'"' . $selected . '>'.$titleText.'</option>';
												}
											}
										} else {
											if ($localizedRow = $TSFE->sys_page->getRecordOverlay($colConfig['foreign_table'], $row2, $this->sys_language_content)) {
												$row2 = $localizedRow;
											}
											$titleText = htmlspecialchars($row2[$titleField],ENT_QUOTES,$charset);
											if ($colConfig['renderMode']=='checkbox' && $this->conf['templateStyle'] == 'css-styled')	{
												$colContent .= '<dt><input class="' . $this->getClassName('checkbox') . '" id="'. $this->getClassName($colName) . '-' . $row2['uid'] .'" name="'.$designator.'['.$colName.']['.$row2['uid']. ']" value="'.$row2['uid'].'" type="checkbox"' . (in_array($row2['uid'], $valuesArray) ? ' checked="checked"' : '') . ' /></dt>
												<dd><label for="'. $this->getClassName($colName) . '-' . $row2['uid'] .'">'.$titleText.'</label></dd>';
											} else {
											 	#debug($titleText);
												$colContent .= '<option value="'.$row2['uid'].'"' . (in_array($row2['uid'], $valuesArray) ? 'selected="selected"' : '') . '>'.$titleText.'</option>';
											}
										}
									}
								}
								if ($colConfig['renderMode'] == 'checkbox' && $this->conf['templateStyle'] == 'css-styled')	{
									$colContent .= '</dl>';
								} else {
									$colContent .= '</select>';
								}
								break;
							default:
								$colContent .= $this->controller->translator->translateContent('unsupported');
								break;
						}
					}
				//debug($colContent,'colContent', __FILE__ , __LINE__);
				return $colContent;
	}
	
	/**
	* Transfers the item array to one where the key corresponds to the value
	* @param	array	array of selectable items like found in TCA
	* @ return	array	array of selectable items with correct key
	*/
	function getItemKeyArray($itemArray) {
		$rc = array();
		if (is_array($itemArray))	{
			foreach ($itemArray as $k => $row)	{
				$key = $row[1];
				$rc [$key] = $row;
			}
		}
		return $rc;
	}	
  
  // getItemKeyArray
	/**
		* Returns the relevant usergroup overlay record fields
		* Adapted from t3lib_page.php
		*
		* @param	mixed		If $usergroup is an integer, it's the uid of the usergroup overlay record and thus the usergroup overlay record is returned. If $usergroup is an array, it's a usergroup record and based on this usergroup record the language overlay record is found and gespeichert.OVERLAYED before the usergroup record is returned.
		* @param	integer		Language UID if you want to set an alternative value to $this->pibase->sys_language_content which is default. Should be >=0
		* @return	array		usergroup row which is overlayed with language_overlay record (or the overlay record alone)
		*/
	function getUsergroupOverlay($usergroup, $languageUid = -1) {
		global $TYPO3_DB;
		// Initialize:
		if ($languageUid < 0) {
			$languageUid = $this->pibase->sys_language_content;
		}
		// If language UID is different from zero, do overlay:
		if ($languageUid) {
			$fieldArr = array('title');
			if (is_array($usergroup)) {
				$fe_groups_uid = $usergroup['uid'];
				// Was the whole record
				$fieldArr = array_intersect($fieldArr, array_keys($usergroup));
				// Make sure that only fields which exist in the incoming record are overlaid!
			} else {
				$fe_groups_uid = $usergroup;
				// Was the uid
			}
			if (count($fieldArr)) {
				$whereClause = 'fe_group=' . intval($fe_groups_uid) . ' ' .
					'AND sys_language_uid='.intval($languageUid). ' ' .
					$this->cObj->enableFields('fe_groups_language_overlay');
				$res = $TYPO3_DB->exec_SELECTquery(implode(',', $fieldArr), 'fe_groups_language_overlay', $whereClause);
				if ($TYPO3_DB->sql_num_rows($res)) {
					$row = $TYPO3_DB->sql_fetch_assoc($res);
				}
			}
		}

			// Create output:
		if (is_array($usergroup)) {
			return is_array($row) ? array_merge($usergroup, $row) : $usergroup;
			// If the input was an array, simply overlay the newfound array and return...
		} else {
			return is_array($row) ? $row : array(); // always an array in return
		}
	}	// getUsergroupOverlay
	
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_feuser_register/lib/class.tx_srfeuserregister_tca.php'])  {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_feuser_register/lib/class.tx_srfeuserregister_tca.php']);
}
?>