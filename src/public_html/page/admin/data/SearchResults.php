
{
	"identifier": "id",
	"items": [
		<?php foreach($this->results as $index => $result): ?>
			<?php echo json_encode(array(
				'id' => $index,
				'registrationId' => $result['registrationId'],
				'eventId' => $this->eventId,
				'regGroupId' => $result['regGroupId'],
				'searchTerm' => $this->searchTerm,
				'fieldName' => $result['displayName'],
				'fieldValue' => $result['value'],
			
				'firstName' => isset($result['firstName'])? $result['firstName'] : '',
				'lastName' => isset($result['lastName'])? $result['lastName'] : '',
				'email' => isset($result['email'])? $result['email'] : '',
			
				'dateRegistered' => $result['dateRegistered'],
				'dateCancelled' => $result['dateCancelled'],
				'showDetailsLink' => $this->showDetailsLink? 'true' : 'false',
				'detailsUrl' => $this->contextUrl("/admin/registration/Registration?eventId={$this->eventId}&id={$result['regGroupId']}#showTab=registrant{$result['registrationId']}"),
				'summaryUrl' => $this->contextUrl("/admin/registration/Summary?eventId={$this->eventId}&regGroupId={$result['regGroupId']}")
			)) ?>
			<?php echo ($index < count($this->results)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}


