
{} && {
	"totalBadges": <?php echo $this->totalBadges ?>,
	"batches": [
	<?php for($i=0; $i<$this->batchCount; ++$i): ?>
		{
			"a": "allBadges",
			"eventId": <?php echo $this->eventId ?>,
			"sortByFieldId": <?echo $this->sortByFieldId ?>,
			"templateIds[]": [<?php echo join(',', $this->templateIds) ?>],
			"batchNumber": <?php echo $i ?>,
			"startDate": "<?php echo $this->startDate ?>",
			"endDate": "<?php echo $this->endDate ?>"
		}
		<?php echo ($i < $this->batchCount-1)? ',' : '' ?>
	<?php endfor; ?>
	]
}

