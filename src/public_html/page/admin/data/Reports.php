
{
	"identifier": "id",
	"items": [
		<?php foreach($this->reports as $index => $report): ?>
		{
			"id": <?php echo $report['id'] ?>,
			"eventId": <?php echo $report['eventId'] ?>,
			"name": "<?php echo $report['name'] ?>",
			"htmlResultsUrl": "<?php echo $this->contextUrl("/admin/report/GenerateReport?eventId={$report['eventId']}&reportId={$report['id']}") ?>",
			"csvResultsUrl": "<?php echo $this->contextUrl("/admin/report/GenerateReport?a=csv&eventId={$report['eventId']}&reportId={$report['id']}") ?>"
		}
		<?php echo ($index < count($this->reports)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

