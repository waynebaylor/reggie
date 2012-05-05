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
						<th>Registration Options</th>
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
			
			$html .= <<<_
				<tr>
					<td>
						{$arrows->html()}
					</td>
					<td>
						{$this->getGroupOptions($group)}
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
								'a' => 'view',
								'id' => $group['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/regOption/SectionRegOptionGroup',
							'parameters' => array(
								'a' => 'removeGroup',
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
	
	private function getGroupOptions($group) {
		$html = '';
		
		foreach($group['options'] as $option) {
			if(isset($option['text'])) {
				$html .= substr($option['text'], 0, 50).'...';
			}
			else {
				$html .= "<div>({$this->escapeHtml($option['code'])}) {$this->escapeHtml($option['description'])}</div>";
			}
		}
		
		if(empty($html)) {
			$html = 'No Registration Options';
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