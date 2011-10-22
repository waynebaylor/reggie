
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
			"manageUrl": "<?php echo $this->contextUrl("/admin/report/ReportList?eventId={$event['id']}") ?>",
			"attendeeUrl": "<?php $url = $this->regFormUrls[$event['id']]['attendeeUrl']; echo $url? $this->contextUrl($url) : '' ?>",
			"exhibitorUrl": "<?php $url = $this->regFormUrls[$event['id']]['exhibitorUrl']; echo $url? $this->contextUrl($url) : '' ?>",
			"specialUrl": "<?php $url = $this->regFormUrls[$event['id']]['specialUrl']; echo $url? $this->contextUrl($url) : '' ?>",
			"canDelete": <?php echo $this->canDelete[$event['id']]? 'true' : 'false' ?>
		}
		<?php echo ($eventIndex < count($this->events)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

