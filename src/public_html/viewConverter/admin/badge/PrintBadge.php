<?php

class viewConverter_admin_badge_PrintBadge extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getSingleBadge($properties) {
		$this->setProperties($properties);
		
		$printTemplate = model_BadgeTemplateType::newTemplate($this->badgeTemplate['type']);
		$pdfInfo = $printTemplate->getPdfSingle(array(
			'user' => $this->user, 
			'event' => $this->eventInfo, 
			'data' => $this->data,
			'margins' => $this->margins,
			'shiftRight' => $this->shiftRight,
			'shiftDown' => $this->shiftDown
		));
		
		return new template_TcpdfWrapper($pdfInfo['pdf'], $pdfInfo['name'].'_'.time().'.pdf', 'I');
	}
	
	public function getAllBadges($properties) {
		$this->setProperties($properties);
		
		$multiBadge = new badgeTemplateType_MultiBadge();
		$pdfInfo = $multiBadge->getPdf(array(
			'user' => $this->user,
			'event' => $this->eventInfo,
			'data' => $this->data
		));
		
		$fileName = "{$pdfInfo['name']}_{$this->batchNumber}.pdf";
		$file = FileUtil::getEventFilesDir($this->eventInfo).'/'.$fileName;
		
		return new template_TcpdfWrapper($pdfInfo['pdf'], $file, 'F');
	}
	
	public function getBatchCount($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_badge_BatchCount'));
	}
}

?>