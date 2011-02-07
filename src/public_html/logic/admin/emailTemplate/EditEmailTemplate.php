<?php

class logic_admin_emailTemplate_EditEmailTemplate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($id) {
		return $this->strictFindById(db_EmailTemplateManager::getInstance(), $id);
	}
	
	public function saveEmailTemplate($template, $regTypeIds) {
		db_EmailTemplateManager::getInstance()->save($template, $regTypeIds);
	}
	
	public function sendTestEmail($emailTemplateId, $toAddress) {
		if(!empty($toAddress)) {
			$template = $this->strictFindById(db_EmailTemplateManager::getInstance(), $emailTemplateId);

			$text = $template['header'].'<div>[Registration Summary]</div>'.$template['footer'];

			EmailUtil::send(array(
				'to' => $toAddress,
				'from' => $template['fromAddress'],
				'bcc' => $template['bcc'],
				'subject' => $template['subject'],
				'text' => $text
			));
		}
	}
}

?>