
{} && {
	"batches": [
	<?php for($i=0; $i<$this->batchCount; ++$i): ?>
		{
			"a": "allBadges",
			"eventId": <?php echo $this->eventId ?>,
			"sortByFieldId": <?echo $this->sortByFieldId ?>,
			"templateIds[]": [<?php echo join(',', $this->templateIds) ?>],
			"batchNumber": <?php echo $i ?>		
		}
		<?php echo ($i < $this->batchCount-1)? ',' : '' ?>
	<?php endfor; ?>
	]
}

