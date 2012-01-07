<?php

class fragment_sectionRegOption_Edit extends template_Template
{
	private $option;
	
	function __construct($option) {
		parent::__construct();
		
		$this->option = $option;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/regOption/RegOption',
			'saveOption',
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-edit">			
				<h3>Edit Registration Option</h3>

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
						'value' => $this->option['eventId']
					))}
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->option['id']
					))}
					{$this->HTML->text(array(
						'name' => 'code',
						'value' => $this->escapeHtml($this->option['code']),
						'size' => '10' 
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Description</td>
				<td>
					{$this->HTML->textarea(array(
						'class' => 'expanding',
						'name' => 'description',
						'value' => $this->escapeHtml($this->option['description']),
						'rows' => 10,
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
							'value' => 'T',
							'checked' => $this->option['defaultSelected'] === 'T'
						))}
					</div>
					<div>
						{$this->HTML->checkbox(array(
							'label' => 'Show Option Price',
							'name' => 'showPrice',
							'value' => 'T',
							'checked' => $this->option['showPrice'] === 'T'
						))}
					</div>
				</td>
			</tr>
			<tr>
				<td class="label">Capacity</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'capacity',
						'value' => $this->escapeHtml($this->option['capacity']),
						'size' => 2
					))}
				</td>
			</tr>
_;
	}
}

?>