<?php

class action_admin_data_User extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_data_User();
		$this->converter = new viewConverter_admin_data_User();
	}
	
	public function view() {
		throw new Exception('Action not implemented: view.');
	}
	
	public function currentUser() {
		$user = SessionUtil::getUser();
		
		$info = $this->logic->currentUser(array('id' => $user['id']));
		return $this->converter->getCurrentUser($info);
	}
}

?>