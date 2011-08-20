
{
	"identifier": "id",
	"items": [
		<?php foreach($this->pages as $index => $page): ?>
		{
			"id": <?php echo $page['id'] ?>,
			"eventId": <?php echo $page['eventId'] ?>,
			"name": "<?php echo $page['name'] ?>",
			"title": "<?php echo $page['title'] ?>",
			"url": "<?php echo $page['url'] ?>"
		}
		<?php echo ($index < count($this->pages)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

