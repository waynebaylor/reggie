<?php

class badgeTemplateType_ThreeByFour extends badgeTemplateType_BaseTemplate
{
	function __construct() {
		parent::__construct();
		
		$this->badgeWidth = 4.0;  // inches
		$this->badgeHeight = 3.0; // inches
		
		$this->topMargin = 1.0 + 0.375;		// inches (3/8in fudge factor)
		$this->sideMargin = 0.25 - 0.125;	// inches (1/8in fudge factor)
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
		
	}
}

?>