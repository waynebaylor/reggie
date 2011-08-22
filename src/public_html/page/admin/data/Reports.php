
{
	"identifier": "id",
	"items": [
		<?php foreach($this->reports as $index => $report): ?>
		{
			"id": <?php echo $report['id'] ?>,
			"eventId": <?php echo $report['eventId'] ?>,
			"name": "<?php echo $report['name'] ?>"
		}
		<?php echo ($index < count($this->reports)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

