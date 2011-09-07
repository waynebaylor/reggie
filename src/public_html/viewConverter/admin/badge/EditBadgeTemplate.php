<?php

class viewConverter_admin_badge_EditBadgeTemplate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Edit Badge Template';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= $this->getFileContents('page_admin_badge_EditBadgeTemplate');
		
		return $body;
	}
	
	public function getAddBadgeCell($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_badge_TemplateCells'));
	}
	
	public function getSaveTemplate($properties) {
		$this->setProperties($properties);
		
		return new fragment_Success();
	}
	
	public function getSaveCellDetails($properties) {
		$this->setProperties($properties);
		
		$success = new fragment_Success();
		$preview = $this->templateType->getHtml($this->template, $this->selectedCell['id']);
		
		$response = <<<_
			{$success->html()}
			<div id="save-cell-preview">
				<h3>Preview</h3>
				{$preview}
			</div>
_;

		return new template_TemplateWrapper($response);
	}
	
	public function getAddCellContent($properties) {
		$this->setProperties($properties);
		
		$detailsHtml = $this->getFileContents('page_admin_badge_CellDetails');
		$cellsHtml = $this->getFileContents('page_admin_badge_TemplateCells');
		
		return new template_TemplateWrapper($detailsHtml.'<div id="add-content-template-cells" class="hide">'.$cellsHtml.'</div>');
	}
	
	public function getRemoveCellContent($properties) {
		$this->setProperties($properties);
		
		$detailsHtml = $this->getFileContents('page_admin_badge_CellDetails');
		$cellsHtml = $this->getFileContents('page_admin_badge_TemplateCells');
		
		return new template_TemplateWrapper($detailsHtml.'<div id="add-content-template-cells" class="hide">'.$cellsHtml.'</div>');
	}
	
	public function getMoveCellContentUp($properties) {
		$this->setProperties($properties);
		
		$detailsHtml = $this->getFileContents('page_admin_badge_CellDetails');
		$cellsHtml = $this->getFileContents('page_admin_badge_TemplateCells');
		
		return new template_TemplateWrapper($detailsHtml.'<div id="add-content-template-cells" class="hide">'.$cellsHtml.'</div>');
	}
	
	public function getMoveCellContentDown($properties) {
		$this->setProperties($properties);
		
		$detailsHtml = $this->getFileContents('page_admin_badge_CellDetails');
		$cellsHtml = $this->getFileContents('page_admin_badge_TemplateCells');
		
		return new template_TemplateWrapper($detailsHtml.'<div id="add-content-template-cells" class="hide">'.$cellsHtml.'</div>');
	}
	
	public function getRemoveBadgeCell($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect("/admin/badge/EditBadgeTemplate?id={$this->template['id']}&eventId={$this->eventId}#template-layout");
	}
}

?>