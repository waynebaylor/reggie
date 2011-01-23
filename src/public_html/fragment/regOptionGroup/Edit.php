<?php

class fragment_regOptionGroup_Edit extends template_Template
{
	private $group;
	
	function __construct($group) {
		parent::__construct();
		
		$this->group = $group;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/regOption/RegOptionGroup', 
			'saveGroup', 
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-edit">
				<h3>Edit Registration Option Group</h3>
				
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="label">Name</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->group['id']
					))}
					{$this->HTML->text(array(
						'name' => 'description',
						'value' => $this->escapeHtml($this->group['description'])
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Restrictions</td>
				<td>
					<div>
						{$this->HTML->checkbox(array(
							'label' => 'Required',
							'name' => 'required',
							'value' => 'true',
							'checked' => $this->group['required']
						))}
					</div>
					<div>
						{$this->HTML->checkbox(array(
							'label' => 'Allow Multiple',
							'name' => 'multiple',
							'value' => 'true',
							'checked' => $this->group['multiple']
						))}
					</div>
				</td>
			</tr>
_;
	}
}

?>