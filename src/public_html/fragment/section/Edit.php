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
			'/admin/section/Section', 
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
		
		$showText = model_Section::containsText($this->section)? '' : 'hide';
		
		return <<<_
			<tr>
				<td class="required label">Name</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->escapeHtml($this->section['id'])
					))}
					{$this->HTML->hidden(array(
						'name' => 'contentTypeId',
						'value' => $this->escapeHtml($this->section['contentType']['id'])
					))}
					
					{$this->HTML->text(array(
						'name' => 'name',
						'value' => $this->escapeHtml($this->section['name'])
					))}
				</td>
			</tr>
			<tr class="{$showText}">
				<td class="label">Text</td>
				<td>
					{$this->HTML->textarea(array(
						'name' => 'text',
						'value' => $this->escapeHtml($this->section['text']),
						'rows' => 10,
						'cols' => 75
					))}
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