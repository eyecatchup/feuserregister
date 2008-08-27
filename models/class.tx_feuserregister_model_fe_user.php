<?php
class tx_feuserregister_model_fe_user {
	
	var $controller;
	var $fe_user;
	
	function tx_feuserregister_model_fe_user($entry, $controller){
		$this->controller = &$controller;
		$this->fe_user = $entry;
	}
	
	function getUserName(){
#		echo $this->fe_user->get('username') . '<br>';
#		echo '<pre>'.print_r($this->fe_user, true).'</pre>';
		return $this->fe_user->get('username');
	}

	
}
?>