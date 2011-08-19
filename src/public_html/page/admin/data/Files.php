
{
	"identifier": "id",
	"items": [
		<?php foreach($this->fileData as $index => $file): ?>
		{
			"id": <?php echo $index+1 ?>,
			"eventId": <?php echo $this->eventId ?>,
			"name": "<?php echo $file['name'] ?>",
			"link": "<?php echo $file['link'] ?>"
		}
		<?php echo ($index < count($this->fileData)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}


