<?php

class fragment_page_List extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Pages</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th></th>
						<th>Title</th>
						<th>Visible To</th>
						<th>Options</th>
					</tr>
					{$this->getPages()}
				</table>
			</div>
_;
	}
	
	private function getPages() {
		$html = '';
		$evenRow = true;
		
		$pages = $this->event['pages'];
		foreach($pages as $page) {
			$arrows = new fragment_Arrows(array(
				'href' => '/action/admin/page/Page',
				'parameters' => array(
					'eventId' => $this->event['id']
				),
				'up' => array(
					'action' => 'movePageUp',
					'id' => $page['id']
				),
				'down' => array(
					'action' => 'movePageDown',
					'id' => $page['id']
				)
			));
			
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd'; 
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>
						{$arrows->html()}
					</td>
					<td>{$page['title']}</td>
					<td>
						{$this->getAvailableTo($page)}
					</td>
					<td class="order">
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/action/admin/page/Page',
							'parameters' => array(
								'action' => 'view',
								'id' => $page['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/action/admin/page/Page',
							'parameters' => array(
								'action' => 'removePage',
								'id' => $page['id']
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		
		return $html;
	}
	
	private function getAvailableTo($page) {
		$html = '';
		
		$pageCategories = $page['visibleTo'];
		foreach($pageCategories as $pageCategory) {
			$html .= '<div>'.$pageCategory['displayName'].'</div>';
		}

		return $html;
	}
}
?>