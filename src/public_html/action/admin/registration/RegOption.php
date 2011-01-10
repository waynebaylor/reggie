<?php

class action_admin_registration_RegOption extends action_ValidatorAction
{
	public function addRegOption() {
		
	}
	
	public function cancelRegOption() {
		$id = RequestUtil::getValue('id', 0); // the Registration_RegOption id.
		$comments = RequestUtil::getValue('comments', '');
		$groupId = RequestUtil::getValue('groupId', 0);
		$reportId = RequestUtil::getValue('reportId', 0);
		
		db_reg_RegOptionManager::getInstance()->cancel($id, $comments);
				
		return new template_Redirect("/admin/registration/Registration?a=view&groupId={$groupId}&reportId={$reportId}");
	}
}

?>