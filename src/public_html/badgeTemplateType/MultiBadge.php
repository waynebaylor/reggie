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
			
			$this->writeData($pdf, $position, $template->getMargins(), $data);
			
			$position['x'] += $template->badgeWidth;
			$position['y'] += $template->badgeHeight;
			
			if($position['y'] > $pageHeight) {
				$pdf->AddPage();
				$position = array('x' => 0, 'y' => 0);
			}

			$template->writeData($pdf, $position, $template->getMargins(), $data);
		}
		
		$pdf->Output('all_badges.pdf', 'I');
	}
}

?>