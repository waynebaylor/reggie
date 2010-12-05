<?php

class fragment_regType_List extends template_Template
{
	private $section;
	private $event;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->event = $event;
		$this->section = $section;	
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Registration Types</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th></th>
						<th>Description</th>
						<th>Visible To</th>
						<th>Code</th>
						<th>Options</th>
					</tr>
					{$this->getRegTypes()}
				</table>
			</div>
_;
	}
	
	private function getRegTypes() {
		$html = '';
		$evenRow = true;
		
		$regTypes = $this->section['content'];
		foreach($regTypes as $type) {
			$arrows = new fragment_Arrows(array(
				'href' => '/admin/regType/RegType',
				'parameters' => array(
					'eventId' => $this->event['id']
				),
				'up' => array(
					'action' => 'moveRegTypeUp',
					'id' => $type['id']
				),
				'down' => array(
					'action' => 'moveRegTypeDown',
					'id' => $type['id']
				)
			));
			
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd';
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>
						{$arrows->html()}
					</td>
					<td>{$this->escapeHtml($type['description'])}</td>
					<td>
						{$this->getVisibleTo($type)}
					</td>
					<td>{$type['code']}</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/admin/regType/RegType',
							'parameters' => array(
								'action' => 'view',
								'id' => $type['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/regType/RegType',
							'parameters' => array(
								'action' => 'removeRegType',
								'id' => $type['id'],
								'eventId' => $this->event['id']
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		
		return $html;
	}
	
	private function getVisibleTo($type) {
		$html = '';
		
		$typeCategories = $type['visibleTo'];
		foreach($typeCategories as $typeCategory) {
			$html .= '<div>'.$typeCategory['displayName'].'</div>';
		}

		return $html;
	}
}
?>