<?php

class fragment_sectionRegOption_Add extends template_Template
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $group;
	}
	
	public function html() {
		$addOptForm = new fragment_XhrAddForm(
			'Add Reg Option',
			'/admin/regOption/RegOption',
			'addOption',
			$this->getFormRows()
		);
		
		$addTextForm = new fragment_XhrAddForm(
			'Add Text',
			'/admin/regOption/RegOption',
			'addText',
			$this->getTextFormRows()
		);
		
		return <<<_
			<div class="fragment-add">
				{$addOptForm->html()}
			</div>
			<div class="sub-divider"></div>
			<div class="fragment-add">
				{$addTextForm->html()}
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
						'name' => 'parentGroupId',
						'value' => $this->group['id']
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
						'cols' => 75
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Restrictions</td>
				<td>
					<div>
						{$this->HTML->checkbox(array(
							'label' => 'Selected By Default',
							'name' => 'defaultSelected',
							'value' => 'T'
						))}
					</div>
					<div>
						{$this->HTML->checkbox(array(
							'label' => 'Show Option Price',
							'name' => 'showPrice',
							'value' => 'T'
						))}
					</div>
				</td>
			</tr>
			<tr>
				<td class="label">Capacity</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'capacity',
						'value' => '',
						'size' => '2'
					))}
				</td>
			</tr>
_;
	}
	
	private function getTextFormRows() {
		return <<<_
			<tr>
				<td class="label required">Text</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'parentGroupId',
						'value' => $this->group['id']
					))}
					<textarea class="expanding" name="text"></textarea>
				</td>
			</tr>
_;
	}
}

?>