<?php

class badgeTemplateType_ThreeByFourDouble extends badgeTemplateType_BaseTemplate
{
	function __construct() {
		parent::__construct();
		
		$this->badgeWidth = 8.0; 	// inches
		$this->badgeHeight = 3.0;	// inches
		
		$this->topMargin = 1.0 + 0.375;		// inches (3/8in fudge factor)
		$this->sideMargin = 0.25 - 0.125;	// inches (1/8in fudge factor)
	}
	
	public function getPdfSingle($config) {
		$user = $config['user'];
		$event = $config['event'];
		$data = $config['data'];
		
		$margins = $this->getMargins(false, $config['shiftDown'], $config['shiftRight']);
		
		$pdf = $this->createTcpdf(array(
			'orientation' => 'L',
			'creator' => $user['email'],
			'author' => $user['email'],
			'title' => $event['code'],
			'subject' => $event['code'],
			'sideMargin' => $margins['side'],
			'topMargin' => $margins['top']
		));

		$pdf->AddPage();
		
		$position = array('x' => 3.0, 'y' => 2.75);
		
		$this->writeData($pdf, $position, $margins, $data);
		
		return array(
			'pdf' => $pdf,
			'name' => 'single_badge'
		);
	}
}

?>