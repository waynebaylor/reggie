<?php

class action_admin_report_GenerateReport extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_GenerateReport();
		$this->converter = new viewConverter_admin_report_GenerateReport();
	}
	
	public function view() {
		$reportId = RequestUtil::getValue('id', 0);
		
		$info = $this->logic->view($reportId);
		
		return $this->converter->getView($info);
	}
	
	public function csv() {
		$reportId = RequestUtil::getValue('id', 0);
		
		$info = $this->logic->csv($reportId);
		
		return $this->converter->getCsv($info);
	}
	
	public function search() {
		$params = RequestUtil::getValues(array(
			'reportId' => 0,
			'eventId' => 0,
			'term' => '',
			'contactFieldId' => ''
		));
		
		$info = $this->logic->search($params);
		
		return $this->converter->getSearch($info);
	}
}

?>