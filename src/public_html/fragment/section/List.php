<?php

class fragment_section_List extends template_Template
{
	private $page;
	
	function __construct($page) {
		parent::__construct();
		
		$this->page = $page;	
	}	
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Sections</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th></th>
						<th>Name</th>
						<th>Content</th>
						<th>Options</th>
					</tr>
					{$this->getSections()}
				</table>
			</div>
_;
	}
	
	private function getSections() {
		$html = '';
		$evenRow = true;
		
		$sections = $this->page['sections'];
		foreach($sections as $section) {
			$arrows = new fragment_Arrows(array(
				'href' => '/action/admin/section/Section',
				'parameters' => array(
					'eventId' => $this->page['eventId']
				),
				'up' => array(
					'action' => 'moveSectionUp',
					'id' => $section['id']
				),
				'down' => array(
					'action' => 'moveSectionDown',
					'id' => $section['id']
				)
			));
			
			$contentType = model_ContentType::valueOf($section['contentType']['id']);
			$contentTypeName = $contentType['name'];
			
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd'; 
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>
						{$arrows->html()}
					</td>
					<td>{$this->escapeHtml($section['name'])}</td>
					<td>
						{$contentTypeName}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/action/admin/section/Section',
							'parameters' => array(
								'action' => 'view',
								'id' => $section['id'],
								'eventId' => $this->page['eventId']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/action/admin/section/Section',
							'parameters' => array(
								'action' => 'removeSection',
								'id' => $section['id']
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		
		return $html;
	}
}

?>