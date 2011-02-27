<?php

class fragment_regOptionGroup_List extends template_Template
{
	private $option;
	private $event;
	
	function __construct($event, $option) {
		parent::__construct();
		
		$this->option = $option;
		$this->event = $event;
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
		
		$groups = $this->option['groups'];
		foreach($groups as $group) {
			$arrows = new fragment_Arrows(array(
				'href' => '/admin/regOption/RegOptionGroup',
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
							'href' => '/admin/regOption/RegOptionGroup',
							'parameters' => array(
								'action' => 'view',
								'id' => $group['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/regOption/RegOptionGroup',
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
	
	private function getGroupOptions($group) {
		$html = '';
		
		foreach($group['options'] as $option) {
			$html .= <<<_
				<div>({$this->escapeHtml($option['code'])}) {$this->escapeHtml($option['description'])}</div>		
_;
		}
		
		if(empty($html)) {
			$html = 'No Registration Options';
		}
		
		return $html;
	}
	
	private function getMultipleRestrictions($group) {
		if($group['multiple'] === 'T') {
			return <<<_
				<div>
					Minimum Required: {$group['minimum']}
				</div>
				<div>
					Maximum Allowed: {$group['maximum']}
				</div>
_;
		}
		else {
			return '';
		}
	}
}

?>