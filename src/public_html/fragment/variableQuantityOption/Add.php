<?php

class fragment_variableQuantityOption_Add extends template_Template
{
	private $event;
	private $section;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->event = $event;
		$this->section = $section;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Variable Quantity Option',
			'/admin/regOption/VariableQuantity',
			'addOption',
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
				<td class="required label">Code</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'sectionId',
						'value' => $this->section['id']
					))}
					{$this->HTML->text(array(
						'name' => 'code',
						'value' => ''
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Description</td>
				<td>
					{$this->HTML->textarea(array(
						'name' => 'description',
						'value' => '',
						'rows' => 5,
						'cols' => 50
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Capacity</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'capacity',
						'value' => '',
						'size' => 2
					))}
				</td>
			</tr>
_;
	}
}

?>