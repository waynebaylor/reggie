
{
	"identifier": "id",
	"items": [
		<?php foreach($this->reports as $index => $report): ?>
			<?php echo json_encode(array(
				'id' => $report['id'],
				'eventId' => $report['eventId'],
				'name' => $report['name'],
				'type' => $report['type'],
				'htmlResultsUrl' => $this->contextUrl("/admin/report/GenerateReport?eventId={$report['eventId']}&reportId={$report['id']}"),
				'csvResultsUrl' => $this->contextUrl("/admin/report/GenerateReport?a=csv&eventId={$report['eventId']}&reportId={$report['id']}")
			)) ?>
			<?php echo ($index < count($this->reports)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

