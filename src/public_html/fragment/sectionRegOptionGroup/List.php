<?php

class fragment_sectionRegOptionGroup_List extends template_Template
{
	private $event;
	private $section;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->event = $event;
		$this->section = $section;
	}
	
	public function html() {
		return <<<_
			<h3>Registration Option Groups</h3>

			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th></th>
						<th>Description</th>
						<th>Restrictions</th>
						<th>Options</th>
					</tr>
					{$this->getGroups()}
				</table>
			</div>
_;
	}
	
	private function getGroups() {
		$html = '';
		$evenRow = true;
		
		$groups = $this->section['content'];
		foreach($groups as $group) {
			$arrows = new fragment_Arrows(array(
				'href' => '/admin/regOption/SectionRegOptionGroup',
				'parameters' => array(
					'eventId' => $this->event['id']
				),
				'up' => array(
					'action' => 'moveGroupUp',
					'id' => $group['id']
				),
				'down' => array(
					'action' => 'moveGroupDown',
					'id' => $group['id']
				)
			));
			
			$required = $group['required'] === 'T'? 'Required' : '';
			$multiple = $group['multiple'] === 'T'? 'Allow Multiple' : '';
			
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd';
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>
						{$arrows->html()}
					</td>
					<td>
						{$this->escapeHtml($group['description'])}
					</td>
					<td>
						<div>
							{$required}
						</div>
						<div>
							{$multiple}
						</div>
						{$this->getMultipleRestrictions($group)}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/admin/regOption/SectionRegOptionGroup',
							'parameters' => array(
								'action' => 'view',
								'id' => $group['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/regOption/SectionRegOptionGroup',
							'parameters' => array(
								'action' => 'removeGroup',
								'id' => $group['id'],
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
	
	private function getMultipleRestrictions($group) {
		if($group['multiple'] === 'T') {
			$min = $group['minimum'];
			$max = $group['maximum'];
			
			$min = $min > 0? "<div>Minimum Required: {$min}</div>" : '';
			
			$max = $max > 0? "<div>Maximum Allowed: {$max}</div>" : '';
			
			return $min.$max;
		}
		else {
			return '';
		}
	}
}

?>