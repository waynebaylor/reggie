
{
	"identifier": "index",
	"showSummaryLink": <?php echo $this->showSummaryLink? 'true' : 'false' ?>,
	"showDetailsLink": <?php echo $this->showDetailsLink? 'true' : 'false' ?>,
	"headings": [
		<?php foreach($this->info['headings'] as $headingIndex => $heading): ?>
		"<?php echo str_replace("\n", ' ', $this->escapeHtml($heading)) ?>"
		
		<?php echo ($headingIndex < count($this->info['headings'])-1)? ',' : '' ?>
		<?php endforeach; ?>
	],
	"items": [
		<?php foreach($this->info['rows'] as $rowIndex => $row): ?>
		{
			"index": <?php echo $rowIndex ?>,
			<?php if($this->showSummaryLink || $this->showDetailsLink): ?>
			"detailsUrl": "<?php echo $this->contextUrl("/admin/registration/Registration?eventId={$this->eventId}&id={$row['regGroupId']}") ?>",
			"summaryUrl": "<?php echo $this->contextUrl("/admin/registration/Summary?eventId={$this->eventId}&regGroupId={$row['regGroupId']}&reportId={$this->reportId}") ?>",
			<?php endif; ?>
			"data": [
				<?php foreach($row['data'] as $dataIndex => $dataItem): ?>
				"<?php echo str_replace("\n", ' ', $this->escapeHtml($dataItem)) ?>"
				<?php echo ($dataIndex < count($row['data']))? ',' : '' ?>
				<?php endforeach; ?>
			]
		}
		<?php echo ($rowIndex < count($this->info['rows'])-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

