<?php

class fragment_sectionRegOptionGroup_Edit extends template_Template
{
	private $group;
	private $action;
	
	function __construct($group, $action) {
		parent::__construct();
		
		$this->action = $action;
		$this->group = $group;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			$this->action, 
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
		$rangeClass = $this->group['multiple'] === 'true'? '' : 'hide';
		
		return <<<_
			<tr>
				<td class="label">Description</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->group['id']
					))}
					{$this->HTML->textarea(array(
						'name' => 'description',
						'value' => $this->escapeHtml($this->group['description']),
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
							'label' => 'Required',
							'name' => 'required',
							'value' => 'true',
							'checked' => $this->group['required']
						))}
					</div>
					<div>
						{$this->HTML->checkbox(array(
							'id' => 'allow-multiple',
							'label' => 'Allow Multiple',
							'name' => 'multiple',
							'value' => 'true',
							'checked' => $this->group['multiple']
						))}
					</div>
					<div class="restriction multiple-allowed {$rangeClass}">
						{$this->HTML->text(array(
							'id' => 'minimum',
							'name' => 'minimum',
							'size' => 2,
							'value' => $this->group['minimum']
						))}
						<label for="minimum">Minimum Required</label>
					</div>
					<div class="restriction multiple-allowed {$rangeClass}">
						{$this->HTML->text(array(
							'id' => 'maximum',
							'name' => 'maximum',
							'size' => 2,
							'value' => $this->group['maximum']
						))}
						<label for="maximum">Maximum Allowed</label>
					</div>
				</td>
			</tr>
_;
	}
}

?>