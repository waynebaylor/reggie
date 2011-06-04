<?php

class action_admin_badge_PrintBadge extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function singleBadge() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0, 
			'registrationId' => 0,
			'badgeTemplateId' => 0
		));
		
		//////////////////////////
		
		$badgeTemplate = $this->strictFindById(db_BadgeTemplateManager::getInstance(), $params['badgeTemplateId']);
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$user = SessionUtil::getUser();
		
		$data = array();
		foreach($badgeTemplate['cells'] as $cell) {
			$data[] = array(
				'font' => $cell['font'],
				'fontSize' => $cell['fontSize'],
				'xCoord' => $cell['xCoord'],
				'yCoord' => $cell['yCoord'],
				'width' => $cell['width'],
				'text' => $this->getCellText($params['registrationId'], $badgeTemplate, $cell),
				'align' => $cell['horizontalAlign']
			);
		}
		
		$printTemplate = new printTemplate_MM();
		$printTemplate->getPdf($user, $eventInfo, $data);
		
		return new fragment_Empty();
	}
	
	private function getCellText($registrationId, $badgeTemplate, $cell) {
		$text = '';
		
		foreach($cell['content'] as $subCell) {
			if(empty($subCell['contactFieldId'])) {
				$text .= $subCell['text'];
			}
			else {
				// get registrant value for contact field.
			}
		}
		
		return $text;
	}
}

?>