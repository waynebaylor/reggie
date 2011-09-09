<?php

abstract class badgeTemplateType_BaseTemplate
{
	public static $BARCODE_WIDTH = 3;    // inches
	public static $BARCODE_HEIGHT = 0.5; // inches
	
	function __construct() {
		$this->badgeWidth = 0;
		$this->badgeHeight = 0;
		
		$this->topMargin = 0;
		$this->sideMargin = 0;
	}
	
	public function getHtml($template, $selectedCellId) {
		$cellsHtml = '';
		$summaries = page_admin_badge_Helper::badgeCellSummaries($template, $selectedCellId);
		foreach($summaries as $summary) {
			$cell = $summary['cell'];
			
			$font = ($cell['font'] === 'helvetica')? 'arial' : $cell['font'];
			if($cell['horizontalAlign'] == 'L') {
				$textAlign = 'left';
			}
			else if($cell['horizontalAlign'] === 'C') {
				$textAlign = 'center';
			}
			else if($cell['horizontalAlign'] === 'R') {
				$textAlign = 'right';
			}
			
			$text = HTML::escapeHtml($summary['text']);
			
			$cssClass = $summary['selected']? 'selected-cell' : 'cell';
			
			if($cell['hasBarcode'] === 'T') {
				$barcodeUrl = Reggie::contextUrl('/images/barcode.gif');
				
				$cellsHtml .= <<<_
					<div class="{$cssClass}" style="
						top:{$cell['yCoord']}in; 
						left:{$cell['xCoord']}in;
						width:{$cell['width']}in;
						font-family:{$font};
						font-size:{$cell['fontSize']}pt;
						text-align:{$textAlign}
					">
						<img class="barcode-placeholder" src="{$barcodeUrl}">
					</div>			
_;
			}
			else {
				$cellsHtml .= <<<_
					<div class="{$cssClass}" style="
						top:{$cell['yCoord']}in; 
						left:{$cell['xCoord']}in;
						width:{$cell['width']}in;
						font-family:{$font};
						font-size:{$cell['fontSize']}pt;
						text-align:{$textAlign}
					">{$text}</div>
_;
			}
		}
		
		return <<<_
			<div id="badge-canvas" style="width:{$this->badgeWidth}in;">
				<div style="width:4in; height:{$this->badgeHeight}in; border-right:1px dotted #777;"></div>
				{$cellsHtml}
			</div>
_;
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
		
		$position = array('x' => (11.0-$this->badgeWidth), 'y' => 2.75);
		
		$this->writeData($pdf, $position, $margins, $data);
		
		return array(
			'pdf' => $pdf,
			'name' => 'single_badge'
		);
	}
	
	protected function createTcpdf($config) {
		require_once 'config/lang/eng.php';
		require_once 'tcpdf.php';
		
		$orientation = ArrayUtil::getValue($config, 'orientation', 'P');
		
		$pdf = new TCPDF($orientation, 'in', 'A4', true, 'UTF-8', false);
		
		$pdf->SetCreator($config['creator']);
		$pdf->SetAuthor($config['author']);
		$pdf->SetTitle($config['title']);
		$pdf->SetSubject($config['subject']);
		
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		
		$pdf->SetMargins(0, 0, 0);
		
		return $pdf;
	}
	
	protected function addCell($pdf, $config) {
		$x = $config['x'];
		$y = $config['y'];
		
		if($config['isBarcode']) { 
			$pdf->SetXY($x, $y);
			
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
			// adjust font size if text width is greater than cell width.
			$fontSize = $config['fontSize'];
			while(($fontSize > 0) && ($config['width'] < $pdf->getStringWidth($config['text'], $config['font'], '', $fontSize))) {
				$fontSize -= 0.1;
			}
			
			$pdf->SetFont(
				/*font family*/ $config['font'], 
				/*font style*/ '', 
				/*font size*/ $fontSize
			);
			
			$height = 0.0138*$fontSize; // (in. per pt.)*(font size in pt.)
			
			// cell mid-point is y, so we need to move it down 1/2 height 
			// so upper left cell coord is at (x, y).
			$adjustedY = $y + 0.5*$height;
			
			$pdf->SetXY($x, $adjustedY); 
			
			$pdf->Cell(
				/*width*/ $config['width'], 
				/*height*/ $height, 
				/*text content*/ $config['text'], 
				/*border*/ 0,
				/*position after drawing cell*/ 0,
				/*alignment*/ $config['align'],
				/*fill background*/ false,
				/*link*/ '',
				/*stretch*/ 1,
				/*cell vertical align*/ 'T',
				/*text content vertical align*/ 'C' 
			);
		}
		return $pdf;
	}
	
	public function writeData($pdf, $position, $margins, $data) {
		foreach($data as $cellData) {
			// set xy position and account for margins and offset.
			$cellData['x'] = $margins['side'] + $position['x'] + $cellData['xCoord'];
			$cellData['y'] = $margins['top'] + $position['y'] + $cellData['yCoord'];
			
			$this->addCell($pdf, $cellData);
		}
		
		return $pdf;
	}
	
	public function getMargins($useMargins = true, $shiftDown = 0, $shiftRight = 0) {
		// calculate margins based on user's input and template dimensions.
		$sideMargin = $shiftRight;
		$topMargin = $shiftDown;
		
		if($useMargins) {
			$sideMargin += $this->sideMargin; 
			$topMargin += $this->topMargin; 
		}
		
		return array(
			'top' => $topMargin,
			'side' => $sideMargin
		);
	}
}

?>