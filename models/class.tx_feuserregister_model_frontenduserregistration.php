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
 * Class that implements the model for table fe_users.
 *
 * model to keep the data manipulating functions for the
 * feuserregister extension
 *
 *
 * @author	Cross Content Media / e-netconsulting <dev@cross-content.com / team@e-netconsulting.de>
 * @package	TYPO3
 * @subpackage	tx_feuserregister
 */

class tx_feuserregister_model_frontenduserregistration
 extends tx_lib_object {

	var $userFolderPID;
 	var $uniqueFeusersColumns = array(
 		'username'
 	);
 	
 	var $noErrors = TRUE;
 	
 	var $fields = array(
 		'username', 
 		'name',
 		'address',
 		'telephone',
 		'fax',
 		'email',
 		'first_name',
 		'last_name',
 		'status',
 		'doubleoptin_code_user',
 		'doubleoptin_code_admin',
 		'pid',
 		'disable',
 		'tstamp',
 		'crdate',
 		'usergroup',
 		'title',
 		'zip',
 		'city',
 		'country',
 		'www',
 		'company',
 		'image',
 		'language',
 		'gender',
 		'date_of_birth',
 		'comments',
 		'by_invitation',
 		'module_sys_dmail_html',
 		'module_sys_dmail_category',
 		'doubleoptin_confirmed_user',
 		'doubleoptin_confirmed_admin',
 		'image',
 		'password',
# 		'tx_ccmtippspielextended_footballclub',
#  		'tx_ccmtippspielextended_footballknowledge'
 	);
 	
 
   function tx_feuserregister_model_frontenduserregistration($controller = null, $parameter = null) {
           parent::tx_lib_object($controller, $parameter);
           $this->userFolderPID = 24;
   }

   function load($parameters = null) {

           // fix settings
           $fields = '*';
           $tables = 'fe_users';
           $groupBy = null;
           $orderBy = '';
           $where = 'disable = 0 AND deleted = 0 ';
           
           // variable settings
           if($parameters) {
           	// query
           	
           	#	$where .= ' AND ' . $parameters;
	        	$query = $GLOBALS['TYPO3_DB']->SELECTquery($fields,$tables,$where,$groupBy,$orderBy);
	        	#echo $query;
	           $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
	        #echo '<pre>'.print_r($result,true).'</pre>';
	           
           	if (FALSE != ($entry = $this->_makeRow($result))) {
	          $this->append($entry);
	        }
#	        echo '<pre>'.print_r($parameters,true).'</pre>';
           }
			else {
	           // query
	           $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
	           #echo "BAR";
	           if($result) {
	           #echo "FOO";
	                   while(FALSE != ($entry = $this->_makeRow($result))) {
	                           $this->append($entry);
	                   }
	           }			
			}
   }

   function loadByConfirmID($confirmColumn, $confirmID){
	   // fix settings
	   $fields = '*';
	   $tables = 'fe_users';
	   $groupBy = '';
	   $orderBy = '';
	   $where = ' '.$this->secureQuery($confirmColumn).'=\''.$this->secureQuery($confirmID).'\' AND deleted = 0 ';
	   # debug($where);        
	   // query
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
	   #t3lib_div::debug($query);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   #t3lib_div::debug($result);
	   #t3lib_div::debug(mysql_num_rows($result));
	   if($result) {
		   while(FALSE != ($entry = $this->_makeRow($result))) {
	       	$this->append($entry);
	       }
	       $this->setConfirmed($confirmColumn, $confirmID);
	       return $this;
	   } else return FALSE;			
   }
   
   function loadByUID($feuserUID){
   	#t3lib_div::debug($feuserUID);
	   // fix settings
	   $fields = '*';
	   $tables = 'fe_users';
	   $groupBy = null;
	   $orderBy = '';
	   $where = ' uid=\''.$this->secureQuery($feuserUID).'\' AND deleted = 0 AND disable=0';
	           
	   // query
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
	   #t3lib_div::debug($query);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   #t3lib_div::debug($result);
	   #t3lib_div::debug(mysql_num_rows($result));
	   if($result) {
		   while(FALSE != ($entry = $this->_makeRow($result))) {
	       	$this->append($entry);
	       }
	       return $this;
	   } else return FALSE;			
   }
   
   function loadByEmail($email){
	   // fix settings
	   $fields = '*';
	   $tables = 'fe_users';
	   $groupBy = null;
	   $orderBy = '';
	   $where = ' email=\''.$this->secureQuery($email).'\' AND deleted = 0';
	           
	   // query
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
	   #t3lib_div::debug($query);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   #t3lib_div::debug($result);
	   #t3lib_div::debug(mysql_num_rows($result));
	   if($result) {
		   while(FALSE != ($entry = $this->_makeRow($result))) {
	       	$this->append($entry);
	       }
	       return $this;
	   } else return FALSE;			
   }
   
   function loadByUsername($username){
	   // fix settings
	   $fields = '*';
	   $tables = 'fe_users';
	   $groupBy = null;
	   $orderBy = '';
	   $where = ' username=\''.$this->secureQuery($username).'\' AND deleted = 0';
	           
	   // query
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
	   #t3lib_div::debug($query);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   #t3lib_div::debug($result);
	   #t3lib_div::debug(mysql_num_rows($result));
	   if($result) {
		   while(FALSE != ($entry = $this->_makeRow($result))) {
	       	$this->append($entry);
	       }
	       return $this;
	   } else return FALSE;			
   }
   
   function getEmailByUsername($username) {
	   // fix settings
	   $fields = 'email';
	   $tables = 'fe_users';
	   $groupBy = null;
	   $orderBy = '';
	   $where = ' username=\''.$this->secureQuery($username).'\' AND deleted = 0';
	           
	   // query
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
#	   debug($query);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   #t3lib_div::debug($result);
	   #t3lib_div::debug(mysql_num_rows($result));
	   if($result) {
		   if(FALSE != ($email = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result))) {
	       	return $email['email'];
	       }
	   } 
	   return FALSE;			
   	}
   
   function getFieldByUsernameOrEmail($username, $fieldname) {
	   // fix settings
	   $fields = '*';
	   $tables = 'fe_users';
	   $groupBy = null;
	   $orderBy = '';
	   $where = ' username=\''.$this->secureQuery($username).'\' OR email=\''.$this->secureQuery($username).'\' AND deleted = 0';
	           
	   // query
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
#	   debug($query);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   #t3lib_div::debug($result);
	   #t3lib_div::debug(mysql_num_rows($result));
	   if($result) {
		   if(FALSE != ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result))) {
	       	return $row[$fieldname];
	       }
	   } 
	   return FALSE;			
   	}
   
   function getUsernameByEmail($email) {
	   // fix settings
	   $fields = 'username';
	   $tables = 'fe_users';
	   $groupBy = null;
	   $orderBy = '';
	   $where = ' email=\''.$this->secureQuery($email).'\' AND deleted = 0';
	           
	   // query
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $tables, $where, $groupBy, $orderBy);
#	   debug($query);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   #t3lib_div::debug($result);
	   #t3lib_div::debug(mysql_num_rows($result));
	   if($result) {
		   if(FALSE != ($username = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result))) {
	       	return $username['username'];
	       }
	   } 
	   return FALSE;			
   	}
   
   	function setConfirmed($confirmColumn, $confirmID) {
	   // fix settings
	   $fields = '*';
	   $table = 'fe_users';
	   $where = ' '.$this->secureQuery($confirmColumn).'=\''.$this->secureQuery($confirmID).'\' AND deleted = 0 ';
	   $field_values[$this->controller->getConfirmColumnName($confirmColumn)] = 1;
	   // query
	   $query = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $field_values);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   
   }
   
   function activateUser($confirmColumn, $confirmID) {
	   // fix settings
	   $fields = '*';
	   $table = 'fe_users';
	   $where = ' '.$this->secureQuery($confirmColumn).'=\''.$this->secureQuery($confirmID).'\' AND deleted = 0 ';
	   $field_values['disable'] = 0;
	   # debug ($where);
	   // query
	   $query = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $field_values);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
   	# debug(mysql_error());
   }
   
   function getUserConfirmationCode($username) {
	   // fix settings
	   $fields = '*';
	   $table = 'fe_users';
	   $where = ' username=\''.$this->secureQuery($username).'\' OR email=\''.$this->secureQuery($username).'\' AND deleted = 0 ';
	   // query
//	   $select_fields,$from_table,$where_clause,$groupBy='',$orderBy='',$limit=''
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $table, $where);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
	   	if ($row['username']){
	   		return $row['doubleoptin_code_user']; 
	   	}
	   } 
	   return FALSE;
   }
   
   function getAdminConfirmationCode($username) {
	   // fix settings
	   $fields = '*';
	   $table = 'fe_users';
	   $where = ' username=\''.$this->secureQuery($username).'\'  AND deleted = 0 ';
	   // query
//	   $select_fields,$from_table,$where_clause,$groupBy='',$orderBy='',$limit=''
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $table, $where);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
	   	if ($row['username']){
	   		return $row['doubleoptin_code_admin']; 
	   	}
	   } 
	   return FALSE;
   }
   
   function UserConfirmed() {
   	//FIXME: UserConfirmed liefert einfach TRUE zurück
   	return TRUE;
   }
   
   function AdminConfirmed() {
   	//FIXME: AdminConfirmed liefert einfach TRUE zurück
   	return TRUE;
   }
   
   function allConfirmed($confirmColumn, $confirmID)
   {
	   // fix settings
	   $fields = '*';
	   $table = 'fe_users';
	   
	   
	   
	   
   	   $where = $confirmColumn .' = \''.$this->secureQuery($confirmID).'\'';
   	   $registerProcessType = $this->controller->configurations->get('config.registerProcessType');
   	   
   	   if ($registerProcessType == 2){
   	   	$where .= ' AND '.$this->controller->doubleoptin_confirmed_col_admin . '=1';
   	   } else if ($registerProcessType == 3){
   	   	$where .= ' AND '.$this->controller->doubleoptin_confirmed_col_user . '=1';
   	   } else if ($registerProcessType == 4) {
   	   	$where .= ' AND '.$this->controller->doubleoptin_confirmed_col_user . '=1 AND '.$this->controller->doubleoptin_confirmed_col_admin . '=1';
   	   }
   	   
   	   
   	   // query
	   $query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $table, $where);
	   $result = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	   if (mysql_num_rows($result)) {
	   	return TRUE;
	   }
	   return FALSE;
   }
   
   
   function secureQuery($query){
   	if (is_array($query)) {
   		foreach ($query as $key => $value){
   			$tmp[$key] = mysql_escape_string(trim($value));
   		}
   		return $tmp;
   	} else {
   		$query = mysql_escape_string(trim($query));
   	}
//   	echo $query;
   	return $query;
   }
   
   function existsUsername(){
		$whereFields = '1=1';
    	$username = $this->controller->parameters->get('username');
    	if ('' == $username) return TRUE;
    	#t3lib_div::debug('existsusername');
    	#t3lib_div::debug($username);
    	if (!$username) {
    		return FALSE;
    	}
		$whereFields = "username  = '".$this->secureQuery($username)."' AND deleted = 0";
		$query = $GLOBALS['TYPO3_DB']->SELECTquery('*', 'fe_users', $whereFields);
		$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
		if ($res) {
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				return TRUE;
			}
		}
		return FALSE;
   }
   
   function getById($id){
	$where = ' deleted = 0 ';
	$where .= ' AND pid =  ' . (int) $this->userFolderPID;
	$where .= ' AND uid=' . (int) $id;
	$query= $GLOBALS['TYPO3_DB']->SELECTquery('*', 'fe_users', $where);
	$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	if($res){
		return $this->_makeRow($res);
	}
   }
   
	function _makeRow($result){
		if($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
			return new tx_lib_object($row);
		} 
		return FALSE;
	}

  function getInputFields() {
    $steps = $this->controller->getSteps();
#    debug($steps);
#      debug($this->controller->additionalFields['fe_users']);
    if (!empty($steps)) {
      $fields = array();
      foreach ($steps as $stepValues){
        $tmp = array_keys($stepValues['fields.']);
        foreach ($tmp as $key => $field) {
          $tmp2 = $this->controller->_removeDot($field);
#          debug($tmp2);
#          debug($key);
          if (is_array($this->controller->fieldsToRemove['fe_users'])) {
            if (!in_array($tmp2, $this->controller->fieldsToRemove['fe_users'])) {
              $fields[$key] = $tmp2; 
            }
          }
        }
      }
    } else {
      $fields = array();
    }
    if (is_array($this->controller->additionalFields['fe_users'])) {
      foreach ($this->controller->additionalFields['fe_users'] as $addField) {
        if (!in_array($addField, $fields)) {
          $fields[] = $addField;
        }
      }
    }

#    debug($this->controller->additionalFields['fe_users']);
#    debug('fields:');
#    debug($fields);
    return $fields;
  }
   
    /**
   * date_german2mysql
   * wandelt ein traditionelles deutsches Datum
   * nach MySQL (ISO-Date).
   */
  function date_german2mysql($datum) {
      list($tag, $monat, $jahr) = explode(".", $datum);
  
      return sprintf("%04d-%02d-%02d", $jahr, $monat, $tag);
  }
   
  function isUniqueValue($fieldname, $fieldvalue) {
    if ('' == $fieldname || '' == $fieldvalue) return FALSE;
    if (!$fieldname || !$fieldvalue) return FALSE;

    $whereFields = "$fieldname  = '".$this->secureQuery($fieldvalue)."' AND deleted = 0";
	$query = $GLOBALS['TYPO3_DB']->SELECTquery('*', 'fe_users', $whereFields);
	$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
	if ($res) {
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			return FALSE;
		}
	}
	return TRUE;  
  }
  
  function insert(&$parameterObject){
	if ($this->existsUsername()) {
		$this->controller->setError('user_aleady_exists_reload_error', '%%%USER_ALREADY_EXISTS_RELOAD_ERROR%%%');		
		return FALSE;
	}
	$additionalFields = explode(',',$this->controller->configurations->get('config.additionalFields'));
	if (is_array($additionalFields)) {
		$this->fields = array_merge_recursive($this->fields,$additionalFields);
	}
	
#    debug($parameterObject->_iterator);
    $this->controller->hookHandler->call('enterInsert', $this, $parameterObject);
#    debug($this->noErrors);
		$parameterObject->set('tstamp',time());
		$parameterObject->set('crdate',time());
		$parameterObject->set('disable', 0);
		$parameterObject->set('pid', $this->controller->getfeuserFolderUid());
		$insertArray = $parameterObject->getArrayCopy();
		unset($insertArray['action']);
		unset($insertArray['step']);
		unset($insertArray['preview']);
		$fields = $this->getInputFields();
#		debug($fields);
		$tmp = array();
		foreach ($insertArray as $key => $value){
			if (!is_array($value)) {
				$tmp[$key] = $value;
			} else {
				$tmp[$key] = implode(',', $value);
			}
			if (!in_array($key, $this->fields) && !in_array($key, $fields)) {
#			 debug('unset '.$key);
				unset($tmp[$key]);
			}
		}				
#	debug($tmp);	
		$insertArray = $tmp;
		if (isset($insertArray['date_of_birth'])) {
      $insertArray['date_of_birth'] = strtotime($this->date_german2mysql($insertArray['date_of_birth']));
    }
		if ($this->controller->configurations->get('config.defaultUserGroup')){
		  if (!isset($insertArray['usergroup'])) $insertArray['usergroup'] = $this->controller->configurations->get('config.defaultUserGroup');
    }
#    debug($_FILES);
    if ($_FILES[$this->controller->getDefaultDesignator()]['name']['image']) {
      $uploaddir = $this->controller->configurations->get('config.uploadFolder');
      $insertArray['image'] = $uploaddir . $_FILES[$this->controller->getDefaultDesignator()]['name']['image'];
    }
		
		$insertArray['doubleoptin_code_user'] = md5(time());
		
		$insertArray['doubleoptin_code_admin'] = md5(str_shuffle($insertArray['doubleoptin_code_user']));
		
		$this->controller->setDoubleOptInCode('user',$insertArray['doubleoptin_code_user']);
		$this->controller->setDoubleOptInCode('admin',$insertArray['doubleoptin_code_admin']);
		$insertArray = $this->controller->handleProcessType($insertArray);
		$query = $GLOBALS['TYPO3_DB']->INSERTquery('fe_users', $this->secureQuery($insertArray));
#		debug($query);
#		$this->noErrors = 'test';
		$this->controller->hookHandler->call('beforeRegister', $this, $query, $insertArray, $parameterObject);
		if ($this->noErrors === TRUE) {
  		 $res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
  //		debug(mysql_error());
  
  		if($res) {
  			$newuseruid = $GLOBALS['TYPO3_DB']->sql_insert_id();
  			$GLOBALS["TSFE"]->fe_user->setKey("ses","newuserid",$newuseruid);
  			$this->controller->hookHandler->call('afterRegister', $this, $query, $insertArray, $parameterObject);
  			$this->controller->setNewUserUID($newuseruid);
  			$this->controller->handleNextActionSetting();
  			return TRUE;
  		} else {
  			$this->controller->setError('mysqlInsert', mysql_error());
  		}
  		$this->controller->hookHandler->call('failRegister', $this, $query, $insertArray, $parameterObject);
  		return FALSE;
    }
		return FALSE;
	}
	
  function update($parameterObject){
 	
		$additionalFields = explode(',',$this->controller->configurations->get('config.additionalFields'));
		if (is_array($additionalFields)) {
			$this->fields = array_merge_recursive($this->fields,$additionalFields);
		}   	
    	$uid = $this->controller->getFeUserUID();
		
		$parameterObject->set('tstamp',time());
		$parameterObject->set('pid', $this->controller->getfeuserFolderUid());
		$updateArray = $parameterObject->getArrayCopy();
		unset($updateArray['action']);
		unset($updateArray['step']);
		unset($updateArray['preview']);
		unset($updateArray['no_cache']);
		if ($updateArray['password'] == '') {
			unset($updateArray['password']);
		}
		if ($updateArray['image'] == '') {
			unset($updateArray['image']);
		}
		if ($this->controller->isEditSaveStep() === TRUE) {
    	unset($updateArray['username']);
    }
    if ($_FILES[$this->controller->getDefaultDesignator()]['name']['image']) {
      $uploaddir = $this->controller->configurations->get('config.uploadFolder');
      $updateArray['image'] = $uploaddir . $_FILES[$this->controller->getDefaultDesignator()]['name']['image'];
    }

		$tmp = array();
		foreach ($updateArray as $key => $value){
			if (!is_array($value)) {
				$tmp[$key] = $value;
			} else {
				$tmp[$key] = implode(',', $value);
			}
			
			if (!in_array($key, $this->fields)) {
				unset($tmp[$key]);
			}
			
		}
		$updateArray = $tmp;
#		debug($updateArray);
	    foreach ($updateArray as $key => $value) {
	      if (array_key_exists($key, $GLOBALS['TSFE']->fe_user->user)) $GLOBALS['TSFE']->fe_user->user[$key] = $value;
	    }
    
		$updateArray = $this->secureQuery($updateArray);
		
		$where = '1=1 AND uid='.$this->secureQuery($uid);
		$query = $GLOBALS['TYPO3_DB']->UPDATEquery('fe_users', $where, $updateArray);
		$this->controller->hookHandler->call('beforeUpdate', $this, $query, $updateArray, $parameterObject);
		$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
		if($res) {
			$this->controller->hookHandler->call('afterUpdate', $this, $query, $updateArray, $parameterObject);
			return TRUE;
		}
#		t3lib_div::debug($query);
		$this->controller->hookHandler->call('failUpdate', $this, $query, $updateArray, $parameterObject);
		$err = $GLOBALS['TYPO3_DB']->sql_error();
		return FALSE;
	}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/models/class.tx_feuserregister_model_frontenduserregistration.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/models/class.tx_feuserregister_model_frontenduserregistration.php']);
}

?>