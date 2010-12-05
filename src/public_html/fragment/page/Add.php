<?php

class fragment_page_Add extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;	
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Page', 
			'/admin/page/Page', 
			'addPage', 
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}	
	
	public function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Title</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->text(array(
						'name' => 'title',
						'value' => ''
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Visible To</td>
				<td>
					{$this->getCategories()}
				</td>
			</tr>
_;
	}
	
	private function getCategories() {
		$items = array();
		$selectedValues = array();
				
		$categories = model_Category::values();
		foreach($categories as $category) {
			$selectedValues[] = $category['id'];
			
			$items[] = array(
				'label' => $category['displayName'],
				'value' => $category['id']
			);
		}	
			
		return <<<_
			<div>
				{$this->HTML->checkboxes(array(
					'name' => 'categoryIds[]',
					'value' => $selectedValues,
					'items' => $items
				))}
			</div>
_;
	}
}

?>