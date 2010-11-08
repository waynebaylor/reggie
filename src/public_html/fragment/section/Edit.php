<?php

class fragment_section_Edit extends template_Template
{
	private $section;
	
	function __construct($section) {
		parent::__construct();
		$this->section = $section;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/action/admin/section/Section', 
			'saveSection', 
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-edit">
				<h3>Edit Section</h3>
				
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		$showOptions = model_Section::containsContactFields($this->section)? '' : 'hide';
		
		return <<<_
			<tr>
				<td class="label">Description</td>
				<td>
					<input type="hidden" name="id" value="{$this->escapeHtml($this->section['id'])}"/>
					<textarea name="title" rows="5" cols="50">{$this->escapeHtml($this->section['title'])}</textarea>
				</td>
			</tr>
			<tr class="{$showOptions}">
				<td class="label">Options</td>
				<td>
					{$this->HTML->checkbox(array(
						'label' => 'Use Numbered Fields',
						'name' => 'numbered',
						'value' => 'true',
						'checked' => $this->section['numbered'] === 'true'
					))}
				</td>
			</tr>
_;
	}
}

?>