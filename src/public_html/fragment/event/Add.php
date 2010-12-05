<?php

class fragment_event_Add extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Event',
			'/admin/event/EditEvent',
			'addEvent',
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
		
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="label">Title</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'displayName',
						'value' => '',
						'size' => '50',
						'maxlength' => '255'
					))}
				</td>
			</tr>
			<tr>
				<td class=" required label">Code</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'code',
						'value' => '',
						'maxlength' => '255'
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Reg Open</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'regOpen',
						'value' => '',
						'size' => '16',
						'maxlength' => '16'
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Reg Closed</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'regClosed',
						'value' => '',
						'size' => '16',
						'maxlength' => '16'
					))}
				</td>
			</tr>
_;
	}
}

?>