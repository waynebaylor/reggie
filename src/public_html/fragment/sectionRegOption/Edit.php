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
						'name' => 'description',
						'value' => $this->escapeHtml($this->option['description']),
						'rows' => '5',
						'cols' => '50'
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
							'value' => 'true',
							'checked' => $this->option['defaultSelected'] === 'true'
						))}
					</div>
					<div>
						{$this->HTML->checkbox(array(
							'label' => 'Show Option Price',
							'name' => 'showPrice',
							'value' => 'true',
							'checked' => $this->option['showPrice'] === 'true'
						))}
					</div>
				</td>
			</tr>
			<tr>
				<td class="label">Capacity</td>
				<td>
					<input type="text" size="2" name="capacity" value="{$this->escapeHtml($this->option['capacity'])}"/>
				</td>
			</tr>
_;
	}
}

?>