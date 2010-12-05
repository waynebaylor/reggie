<?php

class fragment_report_Add extends template_Template
{
	private $event;

	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Report', 
			'/admin/report/Report',
			'addReport',
			$this->getFormRows()
		);
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Name</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->text(array(
						'name' => 'name',
						'value' => '',
						'maxlength' => 255
					))}
				</td>
			</tr>
_;
	}
}

?>