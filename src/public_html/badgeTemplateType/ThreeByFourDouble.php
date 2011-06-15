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
			$cellsHtml .= <<<_
				<div class="{$cssClass}" style="
					top:{$cell['yCoord']}in; 
					left:{$cell['xCoord']}in;
					width:{$cell['width']}in;
					font-family:{$font};
					font-size:{$cell['fontSize']}pt;
					text-align:{$textAlign}
				">
					{$text}
				</div>
_;
		}
		
		return <<<_
			<div id="badge-canvas" style="width:8in;">
				<div style="width:4in; height:3in; border-right:1px dotted #777;"></div>
				{$cellsHtml}
			</div>
_;
	}
	
	public function getPdfSingle($user, $event, $data) {
		$sideMargin = 0.25; // inches
		$topMargin = 1.0; // inches
		
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
			$pdf->SetFont(
				/*font family*/ $cellData['font'], 
				/*font style*/ '', 
				/*font size*/ $cellData['fontSize']
			);
			
			// set xy position and account for margins.
			$pdf->SetXY($sideMargin+$cellData['xCoord'], $topMargin+$cellData['yCoord']);
			
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
		
		$pdf->Output('badge.pdf', 'I');
	}
}

?>