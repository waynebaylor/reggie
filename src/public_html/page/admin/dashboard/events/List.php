
{
	"identifier": "eventId",
	"items": [
		<?php foreach($this->events as $eventIndex => $event): ?>
		{
			"eventId": <?php echo $event['id'] ?>,
			"title": "<?php echo $event['displayName'] ?>",
			"code": "<?php echo $event['code'] ?>",
			"regOpen": "<?php echo $event['regOpen'] ?>",
			"regClosed": "<?php echo $event['regClosed'] ?>"
		}
		<?php echo ($eventIndex < count($this->events)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}




