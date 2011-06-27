<?php

class badgeTemplateType_ThreeByFourDouble extends badgeTemplateType_BaseTemplate
{
	function __construct() {}
	
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
			<div id="badge-canvas" style="width:8in;">
				<div style="width:4in; height:3in; border-right:1px dotted #777;"></div>
				{$cellsHtml}
			</div>
_;
	}
	
	public function getPdfSingle($config) {
		$user = $config['user'];
		$event = $config['event'];
		$data = $config['data'];
		
		// calculate margins based on user's input and template dimensions.
		$sideMargin = $config['shiftRight'];
		$topMargin = $config['shiftDown'];
		if($config['margins'] === 'T') {
			$sideMargin += 0.25; // inches
			$topMargin += 1.0; // inches
		}
		
		$pdf = $this->createTcpdf(array(
			'creator' => $user['email'],
			'author' => $user['email'],
			'title' => $event['code'],
			'subject' => $event['code'],
			'sideMargin' => $sideMargin,
			'topMargin' => $topMargin
		));
		
		$pdf->AddPage();
		
		foreach($data as $cellData) {
			$cellData['sideMargin'] = $sideMargin;
			$cellData['topMargin'] = $topMargin;
			
			$this->addCell($pdf, $cellData);
		}
		
		$pdf->Output('badge.pdf', 'I');
	}
}

?>