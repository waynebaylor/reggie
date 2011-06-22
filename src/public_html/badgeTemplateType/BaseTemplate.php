<?php

abstract class badgeTemplateType_BaseTemplate
{
	public static $BARCODE_WIDTH = 3;    // inches
	public static $BARCODE_HEIGHT = 0.5; // inches
	
	public abstract function getHtml($template, $selectedCellId);
	
	public abstract function getPdfSingle($user, $event, $data);
	
	protected function createTcpdf($config) {
		require_once 'config/lang/eng.php';
		require_once 'tcpdf.php';
		
		$pdf = new TCPDF('P', 'in', 'A4', true, 'UTF-8', false);
		
		$pdf->SetCreator($config['creator']);
		$pdf->SetAuthor($config['author']);
		$pdf->SetTitle($config['title']);
		$pdf->SetSubject($config['subject']);
		
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		
		$pdf->SetMargins($config['sideMargin'], $config['topMargin'], $config['sideMargin']);
		
		return $pdf;
	}
	
	protected function addCell($pdf, $config) {
		// set xy position and account for margins.
		$pdf->SetXY($config['sideMargin']+$config['xCoord'], $config['topMargin']+$config['yCoord']);
			
		if($config['isBarcode']) { 
			$pdf->write2DBarcode(
				/*barcode content*/ $config['text'], 
				/*barcode type*/ 'PDF417', 
				/*x position*/ '', 
				/*y position*/ '',
				/*width*/ self::$BARCODE_WIDTH,
				/*height*/ self::$BARCODE_HEIGHT,
				/*style*/ array(),
				/*position after drawing*/ 'N',
				/*distort to fit*/ true
			);
		}
		else {
			$pdf->SetFont(
				/*font family*/ $config['font'], 
				/*font style*/ '', 
				/*font size*/ $config['fontSize']
			);
			
			$pdf->Cell(
				/*width*/ $config['width'], 
				/*height*/ 0, 
				/*text content*/ $config['text'], 
				/*border*/ 0,
				/*position after drawing cell*/ 0,
				/*alignment*/ $config['align'],
				/*fill background*/ false,
				/*link*/ '',
				/*stretch*/ 1,
				/*cell vertical align*/ 'T',
				/*text content vertical align*/ 'M' 
			);
		}
		return $pdf;
	}
}

?>