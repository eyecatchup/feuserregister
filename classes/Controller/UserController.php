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
 * $Id: class.tx_feuserregister_controller_userregistration.php 18291 2009-03-25 02:22:50Z multani $
 */

class Tx_FeUserRegister_Controller_UserController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * current frontend user
	 *
	 * @var Tx_FeUserRegister_Domain_Model_FeUser
	 */
	protected $frontendUser;
	
	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {		
	}	

	/**
	 * Index action for this controller. redirect to the new action.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		$this->redirect('new');
	}

	/**
	 * new action for this controller. Displays the create form.
	 *
	 * @return string The rendered view
	 */
	public function newAction() {
	}

	/**
	 * create action for this controller. create a new frontend user.
	 *
	 * @param Tx_FeUserRegister_Domain_Model_FeUser $frontendUser The frontend user to create
	 * @return string The rendered view
	 */
	public function createAction(Tx_FeUserRegister_Domain_Model_FeUser $frontendUser) {
	}

	/**
	 * edit action for this controller. Displays the edit form.
	 *
	 * @return string The rendered view
	 */
	public function editAction() {
	}

	/**
	 * update action for this controller. update the frontend user.
	 *
	 * @param Tx_FeUserRegister_Domain_Model_FeUser $frontendUser The frontend user to update
	 * @return string The rendered view
	 */
	public function updateAction(Tx_FeUserRegister_Domain_Model_FeUser $frontendUser) {
	}
	
}

?>