
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
			
				'firstName' => ($this->eventId == 12)? $result['firstName'] : '',
				'lastName' => ($this->eventId == 12)? $result['lastName'] : '',
				'email' => ($this->eventId == 12)? $result['email'] : '',
				'dateRegistered' => ($this->eventId == 12)? $result['dateRegistered'] : '',
				'dateCancelled' => ($this->eventId == 12)? $result['dateCancelled'] : '',
			
				'showDetailsLink' => $this->showDetailsLink? 'true' : 'false',
				'detailsUrl' => $this->contextUrl("/admin/registration/Registration?eventId={$this->eventId}&id={$result['regGroupId']}"),
				'summaryUrl' => $this->contextUrl("/admin/registration/Summary?eventId={$this->eventId}&regGroupId={$result['regGroupId']}")
			)) ?>
			<?php echo ($index < count($this->results)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}


