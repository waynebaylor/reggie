<?php

class action_admin_staticPage_PageList extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_staticPage_PageList();
		$this->converter = new viewConverter_admin_staticPage_PageList();
	}
	
	public function view() {
		$info = $this->logic->view(array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		));
		return $this->converter->getView($info);
	}
	
	public function listPages() {
		$info = $this->logic->listPages(array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		));
		return $this->converter->getListPages($info);
	}
	
	public function deletePages() {
		$info = $this->logic->deletePages(array(
			'eventId' => RequestUtil::getValue('eventId', 0),
			'pageIds' => RequestUtil::getValueAsArray('pageIds', array())
		));
		
		return $this->converter->getDeletePages($info);
	}
}

?>