
{
	"identifier": "id",
	"items": [
		<?php foreach($this->pages as $index => $page): ?>
			<?php echo json_encode(array(
				'id' => $page['id'],
				'eventId' => $page['eventId'],
				'name' => $page['name'],
				'title' => $page['title'],
				'url' => $page['url']
			)) ?>
			<?php echo ($index < count($this->pages)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

