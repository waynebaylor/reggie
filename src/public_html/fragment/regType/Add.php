<?php

class fragment_regType_Add extends template_Template
{
	private $event;
	private $section;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->event = $event;
		$this->section = $section;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Registration Type', 
			'/admin/regType/RegType', 
			'addRegType', 
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
				<td class="required label">Description</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'sectionId',
						'value' => $this->section['id']
					))}
					{$this->HTML->text(array(
						'name' => 'description',
						'value' => ''
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Code</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'code',
						'value' => ''
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Visible To</td>
				<td>
					{$this->HTML->checkboxes(array(
						'name' => 'categoryIds[]',
						'items' => $this->getCategories()
					))}
				</td>
			</tr>
_;
	}
	
	
	private function getCategories() {
		$items = array();
		
		$categories = model_Category::values();
		foreach($categories as $category) {
			$items[] = array(
				'label' => $category['displayName'],
				'value' => $category['id'],
				'checked' => 'checked' 
			);
		}	
			
		return $items;
	}
}

?>