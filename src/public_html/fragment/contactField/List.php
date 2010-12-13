<?php

class fragment_contactField_List extends template_Template
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
			<h3>Information Fields</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th></th>
						<th>Label</th>
						<th>Code</th>
						<th>Restrictions</th>
						<th>Visible To</th>
						<th>Options</th>
					</tr>
					{$this->getFields()}
				</table>			
			</div>
_;
	}
	
	private function getFields() {
		$html = '';
		$evenRow = true;
		
		$contactFields = $this->section['content'];
		foreach($contactFields as $field) {
			$arrows = new fragment_Arrows(array(
				'href' => '/admin/contactField/ContactField',
				'parameters' => array(
					'eventId' => $this->event['id']
				),
				'up' => array(
					'action' => 'moveFieldUp',
					'id' => $field['id']
				),
				'down' => array(
					'action' => 'moveFieldDown',
					'id' => $field['id']
				)
			));
			
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd'; 
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>
						{$arrows->html()}
					</td>
					<td>
						{$this->escapeHtml($field['displayName'])}
					</td>
					<td>
						{$this->escapeHtml($field['code'])}
					</td>
					<td>
						{$this->getFieldRestrictions($field)}
					</td>
					<td>
						{$this->getFieldVisibleTo($field)}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/admin/contactField/ContactField',
							'parameters' => array(
								'action' => 'view',
								'id' => $field['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/contactField/ContactField',
							'parameters' => array(
								'action' => 'removeField',
								'id' => $field['id'],
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
	
	private function getFieldRestrictions($field) {
		$html = '';
		
		$rules = $field['validationRules'];
		foreach($rules as $rule) {
			$value = ": {$rule['value']}";
			
			// if the validation restriction is a true/false flag,
			// then we don't need to display the value.
			if($rule['value'] === 'true') {
				$value = ''; 
			}
			
			$html .= "<div>{$rule['displayName']}{$value}</div>";
		}
		
		$attributes = $field['attributes'];
		foreach($attributes as $attribute) {
			$html .= "<div>".$attribute['displayName'].": ".$attribute['value']."</div>";
		}		
		
		return $html;
	}
	
	private function getFieldVisibleTo($field) {
		$html = '';

		if($field['visibleToAll']) {
			$html = '<div>All</div>';
		}
		else {
			$regTypes = $field['visibleTo'];
			foreach($regTypes as $regType) {
				$html .= <<<_
					<div>
						({$regType['code']}) {$regType['description']}
					</div>
_;
			}
		}
		
		return $html;
	}
}

