
{
	"identifier": "id",
	"items": [
		<?php foreach($this->fileData as $index => $file): ?>
			<?php echo json_encode(array(
				'id' => $index+1,
				'eventId' => $this->eventId,
				'name' => $file['name'],
				'link' => $file['link']
			)) ?>
			<?php echo ($index < count($this->fileData)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}


