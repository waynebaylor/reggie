
{
	"identifier": "eventId",
	"items": [
		<?php foreach($this->events as $eventIndex => $event): ?>
		{
			"eventId": <?php echo $event['id'] ?>,
			"title": "<?php echo $event['displayName'] ?>",
			"code": "<?php echo $event['code'] ?>",
			"regOpen": "<?php echo substr($event['regOpen'], 0, -3) ?>",
			"regClosed": "<?php echo substr($event['regClosed'], 0, -3) ?>",
			"manageUrl": "<?php echo $this->contextUrl("/admin/event/Manage?id={$event['id']}") ?>"
		}
		<?php echo ($eventIndex < count($this->events)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}




