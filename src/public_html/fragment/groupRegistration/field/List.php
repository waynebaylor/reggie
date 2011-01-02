<?php

class fragment_groupRegistration_field_List extends template_Template
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

			<h3>Default Information Fields</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th>Name</th>
						<th>Options</th>
					</tr>
					{$this->getFields()}					
				</table>
			</div>
_;
	}
	
	private function getFields() {
		$html = '';
		
		$fields = $this->event['groupRegistration']['fields']; 

		foreach($fields as $field) {
			$html .= <<<_
				<tr>
					<td>{$field['displayName']}</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/event/EditGroupRegistration',
							'parameters' => array(
								'a' => 'removeField',
								'id' => $field['id']
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
