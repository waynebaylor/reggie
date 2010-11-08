<?php

class fragment_contactFieldOption_Add extends template_Template
{
	private $event;
	private $field;
	
	function __construct($event, $field) {
		parent::__construct();
		
		$this->field = $field;	
		$this->event = $event;
	}
	
	public function html() {
		$addForm = new fragment_XhrAddForm('Add Option', '/action/admin/contactField/Option', 
											'addOption', $this->getFormRows());
		
		return <<<_
			<div class="fragment-add">
				{$addForm->html()}
			</div>
_;
	}	
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Label</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'contactFieldId',
						'value' => $this->field['id']
					))}
					{$this->HTML->text(array(
						'name' => 'displayName',
						'value' => ''
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Restrictions</td>
				<td>
					{$this->HTML->checkbox(array(
						'label' => 'Selected By Default',
						'name' => 'defaultSelected',
						'value' => 'true'	
					))}
				</td>
			</tr>
_;
	}
}
?>