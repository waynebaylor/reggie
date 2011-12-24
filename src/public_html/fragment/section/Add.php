<?php

class fragment_section_Add extends template_Template
{
	private $page;
	
	function __construct($page) {
		parent::__construct();
		
		$this->page = $page;	
	}
	
	public function html() {
		$form = new fragment_XhrAddForm('Add Section', '/admin/section/Section', 'addSection', $this->getFormRows());
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}
	
	public function getFormRows() {
		return <<<_
			<tr>
					<td class="required label">Name</td>
					<td>
						{$this->HTML->hidden(array(
							'name' => 'eventId',
							'value' => $this->page['eventId']
						))}
						{$this->HTML->hidden(array(
							'name' => 'pageId',
							'value' => $this->page['id']
						))}
						{$this->HTML->text(array(
							'name' => 'name',
							'value' => ''
						))}
					</td>
				</tr>
				<tr>
					<td class="required label">Content</td>
					<td>
						{$this->HTML->select(array(
							'name' => 'contentTypeId',
							'value' => '',
							'items' => $this->getContentTypes()
						))}
					</td>
				</tr>
_;
	}
	
	private function getContentTypes() {
		$opts = array();
		
		$types = model_ContentType::values();
		foreach($types as $index => $type) {
			$opts[] = array(
				'label' => $type['name'],
				'value' => $type['id']
			);
		}
		
		return $opts;
	}
}
?>