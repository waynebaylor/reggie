<?php

abstract class badgeTemplateType_BaseTemplate
{
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
}

?>