<?php

class badgeTemplateType_MultiBadge extends badgeTemplateType_BaseTemplate
{
	function __construct() {
		parent::__construct();
	}

	/**
	 * Not implemented.
	 * @see badgeTemplateType_BaseTemplate::getHtml()
	 */
	public function getHtml($template, $selectedCellId) {
		return '';
	}
	
	/**
	 * Not implemented.
	 * @see badgeTemplateType_BaseTemplate::getPdfSingle()
	 */
	public function getPdfSingle($config) {
		return '';
	}
	
	public function getPdf($config) {
		$user = $config['user'];
		$event = $config['event'];
		
		$pdf = $this->createTcpdf(array(
			'creator' => $user['email'],
			'author' => $user['email'],
			'title' => $event['code'],
			'subject' => $event['code'],
			'sideMargin' => 0,
			'topMargin' => 0
		));
		
		$pdf->AddPage();
		
		$pageHeight = $pdf->getPageHeight();
		$pageWidth = $pdf->getPageWidth();
		
		$position = array('x' => 0, 'y' => 0);
		
		foreach($config['data'] as $index => $badgeData) {
			$template = model_BadgeTemplateType::newTemplate($badgeData['template']['type']);
			$data = $badgeData['data'];

			if($index > 0 && ($index % 3) === 0) {
				$pdf->AddPage();
				$position = array('x' => 0, 'y' => 0);
			}
			else {
				$position['y'] = (($index%3)*$template->badgeHeight);
			}			
			
			$this->writeData($pdf, $position, $template->getMargins(), $data);
		}
		
		return array(
			'pdf' => $pdf, 
			'name' => 'badges'
		);
	}
}

?>