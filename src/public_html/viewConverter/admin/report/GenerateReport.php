<?php

class viewConverter_admin_report_GenerateReport extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	protected function body() {
		$body = parent::body();
		
		$fieldSelect = fragment_reportField_HTML::select($this->event, false);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.ReportResultsGrid");
				
				dojo.addOnLoad(function() {
					var fieldSelect = dojo.query("#search-fields select")[0];
					
					new hhreg.admin.widget.ReportResultsGrid({
						reportId: {$this->reportId},
						eventId: {$this->eventId},
						isSearch: {$this->isSearch},
						showSearchLink: {$this->showSearchLink},
						searchFieldSelectNode: fieldSelect
					}, dojo.place("<div></div>", dojo.byId("results-grid"), "replace")).startup();
				});
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					
					<div id="results-grid"></div>
					<div id="search-fields">
						{$fieldSelect}
					</div>
				</div>
			</div>	
_;
		
		return $body;
	}
	
	public function getCsv($properties) {
		$this->setProperties($properties);
		
		$text = '"'.implode('","', $this->info['headings']).'"';
		$text .= PHP_EOL;
		
		foreach($this->info['rows'] as $row) {
			$text .= '"'.implode('","', $row['data']).'"';
			$text .= PHP_EOL;
		}
		
		$fileName = preg_replace('/\s+/', '_', $this->info['reportName']).'.csv';
		
		header('Content-Type: text/csv');
		
		// f'n IE doesn't recognize it as a downloadable so we have to do 
		// something special.
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
			header("Content-Disposition: inline; filename=\"{$fileName}\"");
		}
		else {
			header("Content-Disposition: attachment; filename=\"{$fileName}\"");
		}
		
		return new template_TemplateWrapper($text);		
	}
	
	public function getSearch($properties) {
		return $this->getView($properties);
	}
}

?>