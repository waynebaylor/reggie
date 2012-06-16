
{
	"identifier": "eventId",
	"items": [
		<?php foreach($this->events as $eventIndex => $event): ?>
			<?php 
				$attendeeUrl = $this->regFormUrls[$event['id']]['attendeeUrl'];
				$exhibitorUrl = $this->regFormUrls[$event['id']]['exhibitorUrl'];
				$specialUrl = $this->regFormUrls[$event['id']]['specialUrl'];
			?>
			<?php echo json_encode(array(
				'eventId' => $event['id'],
				'title' => $event['displayName'],
				'code' => $event['code'],
				'regOpen' => substr($event['regOpen'], 0, -3),
				'regClosed' => substr($event['regClosed'], 0, -3),
				'manageUrl' => $this->contextUrl("/admin/report/ReportList?eventId={$event['id']}"),
				'attendeeUrl' => $attendeeUrl? $this->contextUrl($attendeeUrl) : '',
				'exhibitorUrl' => $exhibitorUrl? $this->contextUrl($exhibitorUrl) : '',
				'specialUrl' => $specialUrl? $this->contextUrl($specialUrl) : '',
				'canDelete' => $this->canDelete[$event['id']]? 'true' : 'false'		
			)) ?>
			<?php echo ($eventIndex < count($this->events)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

