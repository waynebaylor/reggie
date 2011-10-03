<?php

class fragment_groupRegistration_Edit extends template_Template
{
	private $event;

	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/event/EditGroupRegistration', 
			'saveGroupReg', 
			$this->getFormRows()
		);
		
		return <<<_
				<script type="text/javascript">
					dojo.require("hhreg.xhrTableForm");
					
					dojo.addOnLoad(function() {
						dojo.query("#edit-group-reg form").forEach(function(item) {
							hhreg.xhrTableForm.bind(item);		
						});
					});
				</script>
				
				<div id="edit-group-reg">
					{$form->html()}
				</div>
_;
	}
	
	private function getFormRows() {
		$groupReg = $this->event['groupRegistration'];
		$infoClass = $groupReg['enabled'] === 'T'? '' : 'hide';
		
		return <<<_
			<tr>
				<td class="label">Status</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $groupReg['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $groupReg['eventId']
					))}
					
					{$this->HTML->radios(array(
						'name' => 'enabled',
						'value' => $groupReg['enabled'] === 'T'? 'T' : 'F',
						'items' => array(
							array(
								'id' => 'enabled_true',
								'label' => 'Enabled',
								'value' => 'T'
							),
							array(
								'id' => 'enabled_false',
								'label' => 'Disabled',
								'value' => 'F'
							)				
						)	
					))}
				</td>
			</tr>
			<tr class="group-reg-info {$infoClass}">
				<td class="label">Registration Type</td>
				<td>
					{$this->HTML->checkbox(array(
						'label' => 'Default to first registrant\'s selection.',
						'name' => 'defaultRegType',
						'value' => 'T',
						'checked' => $groupReg['defaultRegType']
					))}
				</td>
			</tr>
_;
	}
}

?>