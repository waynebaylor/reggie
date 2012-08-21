<?php

class viewConverter_admin_event_EditMetadata extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$html = $this->getContent();
		return new template_TemplateWrapper($html);
	}
	
	public function getSaveMetadata($properties) {
		$this->setProperties($properties);
		
		return new fragment_Success();
	}
	
	private function getContent() {
		$form = new fragment_XhrTableForm(
			'/admin/event/EditMetadata',
			'saveMetadata',
			$this->getFormRows()
		);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrTableForm");
				
				dojo.addOnLoad(function() {
					dojo.query("#edit-metadata form").forEach(function(item) {
						hhreg.xhrTableForm.bind(item);
					});
				});
			</script>
			
			<div id="edit-metadata">
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		$firstNameSelect = fragment_contactField_HTML::selectByEventId($this->eventId, array(
			'name' => 'firstName',
			'value' => $this->metadataToField['FIRST_NAME'],
			'items' => array(
				array('label' => 'Choose a field...', 'value' => 0)
			)
		));
		
		$lastNameSelect = fragment_contactField_HTML::selectByEventId($this->eventId, array(
			'name' => 'lastName',
			'value' => $this->metadataToField['LAST_NAME'],	
			'items' => array(
				array('label' => 'Choose a field...', 'value' => 0)
			)
		));
		
		$emailSelect = fragment_contactField_HTML::selectByEventId($this->eventId, array(
			'name' => 'email',
			'value' => $this->metadataToField['EMAIL'],
			'items' => array(
				array('label' => 'Choose a field...', 'value' => 0)
			)
		));
		
		return <<<_
			<tr>
				<td class="label">First Name</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->eventId
					))}
					
					{$firstNameSelect}
				</td>
			</tr>
			<tr>
				<td class="label">Last Name</td>
				<td>
					{$lastNameSelect}
				</td>
			</tr>
			<tr>
				<td class="label">Email</td>
				<td>
					{$emailSelect}
				</td>
			</tr>
_;
	}
}