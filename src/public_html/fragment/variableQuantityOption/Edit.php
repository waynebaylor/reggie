<?php

class fragment_variableQuantityOption_Edit extends template_Template
{
	private $option;
	
	function __construct($option) {
		parent::__construct();
		
		$this->option = $option;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/regOption/VariableQuantity', 
			'saveOption', 
			$this->getFormRows());
			
		return <<<_
			<div class="fragment-edit">
				<h3>Edit Variable Quantity Option</h3>
				
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
						'value' => $this->escapeHtml($this->option['code'])
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Description</td>
				<td>
					{$this->HTML->textarea(array(
						'name' => 'description',
						'value' => $this->escapeHtml($this->option['description']),
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
						'value' => $this->escapeHtml($this->option['capacity']),
						'size' => 2
					))}
				</td>
			</tr>
_;
	}
}

?>