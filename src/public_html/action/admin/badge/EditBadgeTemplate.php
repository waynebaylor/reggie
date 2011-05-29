<?php

class action_admin_badge_EditBadgeTemplate extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_badge_EditBadgeTemplate();
		$this->converter = new viewConverter_admin_badge_EditBadgeTemplate();
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'id' => 0
		));
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
}

?>