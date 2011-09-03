
{
	"identifier": "id",
	"items": [
		<?php foreach($this->results as $index => $result): ?>
		{
			"id": <?php echo $index ?>,
			"registrationId": <?php echo $result['registrationId'] ?>,
			"eventId": <?php echo $this->eventId ?>,
			"regGroupId": <?php echo $result['regGroupId'] ?>,
			"searchTerm": "<?php echo $this->searchTerm ?>",
			"fieldName": "<?php echo $result['displayName'] ?>",
			"fieldValue": "<?php echo $result['value'] ?>",
			"detailsUrl": "<?php echo $this->contextUrl("/admin/registration/Registration?eventId={$this->eventId}&groupId={$result['regGroupId']}") ?>",
			"summaryUrl": "<?php echo $this->contextUrl("/admin/registration/Summary?eventId={$this->eventId}&regGroupId={$result['regGroupId']}") ?>"
		}
		<?php echo ($index < count($this->results)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}


