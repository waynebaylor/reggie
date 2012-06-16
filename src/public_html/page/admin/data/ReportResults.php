
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
			<?php 
				$data = array();
				foreach($row['data'] as $dataIndex => $dataItem) {
					$data[] = str_replace("\n", ' ', $this->escapeHtml($dataItem));
				}
				
				$unencoded = array(
					'index' => $rowIndex,
					'data' => $data
				);
				
				if($this->showSummaryLink || $this->showDetailsLink) {
					$unencoded['detailsUrl'] = $this->contextUrl("/admin/registration/Registration?eventId={$this->eventId}&id={$row['regGroupId']}");
					$unencoded['summaryUrl'] = $this->contextUrl("/admin/registration/Summary?eventId={$this->eventId}&regGroupId={$row['regGroupId']}");
				}
			?>
			<?php echo json_encode($unencoded) ?>
			<?php echo ($rowIndex < count($this->info['rows'])-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

