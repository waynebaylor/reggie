<?php

require_once 'config/lang/eng.php';
require_once 'tcpdf.php';
		
class printTemplate_MM
{
	function __construct() {
		$this->marginLeft = 0.25;
		$this->marginRight = 0.25;
		$this->marginTop = 0;
	}
	
	public function getHtml() {
		
	}
	
	public function getPdf($user, $event, $data) {
		$pdf = new TCPDF('P', 'in', 'A4', true, 'UTF-8', false);
		
		$pdf->SetCreator($user['email']);
		$pdf->SetAuthor($user['email']);
		$pdf->SetTitle($event['code']);
		$pdf->SetSubject($event['code']);
		
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		
		$pdf->SetMargins($this->marginLeft, $this->marginTop, $this->marginRight);
	
		$pdf->AddPage();
		
		foreach($data as $cellData) {
			$pdf->SetFont(
				/*font family*/ $cellData['font'], 
				/*font style*/ '', 
				/*font size*/ $cellData['fontSize']
			);
			
			$pdf->SetXY($cellData['xCoord'], $cellData['yCoord']);
			
			$pdf->Cell(
				/*width*/ $cellData['width'], 
				/*height*/ 0, 
				/*text content*/ $cellData['text'], 
				/*border*/ 0,
				/*position after drawing cell*/ 0,
				/*alignment*/ $cellData['align'],
				/*fill background*/ false,
				/*link*/ '',
				/*stretch*/ 1,
				/*cell vertical align*/ 'T',
				/*text content vertical align*/ 'M' 
			);
		}
		
		$pdf->Output('/tmp/reggie_badge.pdf', 'F');
	}
}