<?php

class fragment_sectionRegOptionGroup_Add extends template_Template
{
	private $section;
	private $event;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->section = $section;
		$this->event = $event;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Option Group',
			'/admin/regOption/SectionRegOptionGroup',
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
				<td class="label">Restrictions</td>
				<td>
					<div>
						{$this->HTML->hidden(array(
							'name' => 'eventId',
							'value' => $this->event['id']
						))}
						{$this->HTML->hidden(array(
							'name' => 'sectionId',
							'value' => $this->section['id']
						))}
						{$this->HTML->checkbox(array(
							'id' => 'required_true',
							'label' => 'Required',
							'name' => 'required',
							'value' => 'T'
						))}
					</div>
					<div>
						{$this->HTML->checkbox(array(
							'id' => 'allow-multiple',
							'label' => 'Allow Multiple',
							'name' => 'multiple',
							'value' => 'T'
						))}
					</div>
					<div class="restriction multiple-allowed hide">
						{$this->HTML->text(array(
							'id' => 'minimum',
							'name' => 'minimum',
							'size' => 2,
							'value' => 0
						))}
						<label for="minimum">Minimum Required</label>
					</div>
					<div class="restriction multiple-allowed hide">
						{$this->HTML->text(array(
							'id' => 'maximum',
							'name' => 'maximum',
							'size' => 2,
							'value' => 0
						))}
						<label for="maximum">Maximum Allowed</label>
					</div>
				</td>
			</tr>
_;
	}
}

?>