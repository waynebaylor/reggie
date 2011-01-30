<?php

class fragment_regOptionGroup_Add extends template_Template
{
	private $option;
	private $event;
	
	function __construct($event, $option) {
		parent::__construct();
		
		$this->option = $option;
		$this->event = $event;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Option Group',
			'/admin/regOption/RegOptionGroup',
			'addGroup',
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
				<td class="label">Name</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'regOptionId',
						'value' => $this->option['id']
					))}
					
					{$this->HTML->hidden(array(
						'name' => 'description',
						'value' => ''
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
							'value' => 'true'
						))}
					</div>
					<div>
						{$this->HTML->checkbox(array(
							'id' => 'allow-multiple',
							'label' => 'Allow Multiple',
							'name' => 'multiple',
							'value' => 'true'
						))}
					</div>
					<div class="restriction multiple-allowed hide">
						{$this->HTML->text(array(
							'name' => 'minimum',
							'size' => 2,
							'value' => 1
						))}
						Minimum Required
					</div>
					<div class="restriction multiple-allowed hide">
						{$this->HTML->text(array(
							'name' => 'maximum',
							'size' => 2,
							'value' => 1
						))}
						Maximum Allowed
					</div>
				</td>
			</tr>
_;
	}
}

?>