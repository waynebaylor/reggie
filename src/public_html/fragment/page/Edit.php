<?php

class fragment_page_Edit extends template_Template
{
	private $page;
	
	function __construct($page) {
		parent::__construct();
		
		$this->page = $page;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm('/admin/page/Page', 'savePage', $this->getFormRows());
		
		return <<<_
			<div class="fragment-edit">
				<h3>Edit Page</h3>
				
				{$form->html()}
			</div>
_;
	}	
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Page Title</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->page['id']
					))}
					{$this->HTML->text(array(
						'name' => 'title',
						'value' => $this->escapeHtml($this->page['title']),
						'maxlength' => '255'						
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Available To</td>
				<td>
					{$this->getCategories()}
				</td>
			</tr>
_;
	}

	private function getCategories() {
		$html = '';

		$categories = model_Category::values();
		foreach($categories as $category) {
			$checked = model_EventPage::isVisibleTo($this->page, $category)? 'checked="checked"' : '';
			$html .= <<<_
				<div>
					<input type="checkbox" id="categoryId_{$category['id']}" name="categoryIds[]" value="{$category['id']}" {$checked}/>
					<label for="categoryId_{$category['id']}">{$category['displayName']}</label>
				</div>
_;
		}
			
		return $html;
	}
}

?>