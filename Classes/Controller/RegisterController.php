<?php

class Tx_Feuserregister_Controller_RegisterController extends Tx_Extbase_MVC_Controller_ActionController {
	/**
	 * @var Tx_Extbase_Domain_Repository_FrontendUserRepository
	 */
	protected $frontendUserRepository;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		/*
		 * @TODO:
		 * Das extbae model liefert nur die standard felder. Wie kommen wir mit extbase
		 * an die Felder, die durch andere Extension hinzugeügt wurden?
		 * Z.B. die Felder, die diese Extension mit bringt!?
		 */
		$this->frontendUserRepository = t3lib_div::makeInstance('Tx_Extbase_Domain_Repository_FrontendUserRepository');
	}

	public function indexAction() {
	}

	public function registerAction() {
	}

	public function confirmAction() {
	}
}

?>