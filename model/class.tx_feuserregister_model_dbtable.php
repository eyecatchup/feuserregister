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

/**
 * tx_feuserregister_model_dbtable
 *  
 * @author Frank N�gler
 * @version 
 */

abstract class tx_feuserregister_model_dbtable {
	protected $_table = '';
	
	protected $_data = array ();
	protected $_dataDefinitions = array ();
	
	public function __construct($uid = null) {
		$res = $GLOBALS['TYPO3_DB']->sql_query("describe {$this->_table}");
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			while ( $tableDefinition = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ) {
				$this->_data[$tableDefinition['Field']] = '';
				$this->_dataDefinitions[$tableDefinition['Field']] = $tableDefinition['Type'];
			}
		}
		if ($uid !== null) {
			$this->_data['uid'] = (int) $uid;
		}
		if (intval($this->_data['uid'])) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				$this->_table,
				'uid = ' . $this->_data['uid']
			);
			$this->_data = $rows[0];
		}
	}
	
	public function select($where) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->_table,
			$where
		);
		if (is_array($rows[0])) {
			$this->_data = $rows[0];
		}
	}
	
	public function set($property, $value) {
		return $this->__set($property, $value);
	}
	
	public function get($property) {
		return $this->__get($property);
	}
	
	public function getAttributes() {
		return array_keys($this->_data);
	}
	
	public function __set($name, $value) {
		if (array_key_exists($name, $this->_data)) {
			$this->_data[$name] = $value;
		}
		return $this;
	}
	
	public function __get($name) {
		if (array_key_exists($name, $this->_data)) {
			return $this->_data[$name];
		}
		return null;
	}
	
	public function save() {
		if (intval($this->_data['uid'])) {
			return $this->_doUpdate();
		} else {
			return $this->_doInsert();
		}
	}
	
	protected function _doUpdate() {
		if (array_key_exists('tstamp', $this->_data)) {
			$this->_data['tstamp'] = time();
		}
		$GLOBALS ['TYPO3_DB']->exec_UPDATEquery($this->_table, 'uid = ' . $this->_data['uid'], $this->_prepareDataForDatabase());
		return ($GLOBALS ['TYPO3_DB']->sql_affected_rows ());
	}
	
	protected function _doInsert() {
		if (array_key_exists('tstamp', $this->_data)) {
			$this->_data['tstamp'] = time();
		}
		if (array_key_exists('crdate', $this->_data)) {
			$this->_data['crdate'] = time();
		}
		$GLOBALS ['TYPO3_DB']->exec_INSERTquery($this->_table, $this->_prepareDataForDatabase());
		$this->_data['uid'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		return $this->_data['uid'];
	}
	
	protected function _prepareDataForDatabase() {
		$preparedData = array();
		foreach ($this->_dataDefinitions as $field => $type) {
			switch (strtoupper($type)) {
				case 'TINYINT':
				case 'SMALLINT':
				case 'MEDIUMINT':
				case 'INT':
				case 'INTEGER':
				case 'BIGINT':
				case 'YEAR':
					$preparedData[$field] = intval($this->_data[$field]);
				break;
				case 'FLOAT':
					$preparedData[$field] = floatval($this->_data[$field]);
				break;
				case 'DOUBLE':
				case 'REAL':
					$preparedData[$field] = doubleval($this->_data[$field]);
				break;
				case 'DECIMAL':
				case 'NUMERIC':
				case 'DATE':
				case 'DATETIME':
				case 'TIMESTAMP':
				case 'TIME':
				case 'CHAR':
				case 'VARCHAR':
				case 'TINYBLOB':
				case 'TINYTEXT':
				case 'BLOB':
				case 'TEXT':
				case 'MEDIUMBLOB':
				case 'MEDIUMTEXT':
				case 'LONGBLOB':
				case 'LONGTEXT':
				case 'ENUM':
				case 'SET':
				default:
					$preparedData[$field] = $GLOBALS['TYPO3_DB']->quoteStr($this->_data[$field], $this->_table);
				break;
			}
		}
		return $preparedData;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_dbtable.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_dbtable.php']);
}

?>