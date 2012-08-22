<?php

class viewConverter_admin_search_Search extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = "Search Results";
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.SearchResultsGrid");
				
				dojo.addOnLoad(function() {
					new hhreg.admin.widget.SearchResultsGrid({
						eventId: {$this->eventId},
						metadataFields: {$this->metadataFields},
						searchTerm: "{$this->searchTerm}"
					}, dojo.place("<div></div>", dojo.byId("results-grid"), "replace")).startup();
				});
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					
					<div style="font-style:italic;">
						Showing values starting with: "<span style="font-weight:bold;">{$this->searchTerm}</span>"
					</div>
					
					<div class="sub-divider"></div>
					
					<div id="results-grid"></div>
				</div>
			</div>
_;

		return $body;
	}
	
	public function getListResults($properties) {
		$this->setProperties($properties);
		
		$html = $this->getFileContents('page_admin_data_SearchResults');
		return new template_TemplateWrapper($html);
	}
}

?>