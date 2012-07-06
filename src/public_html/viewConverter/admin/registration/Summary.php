<?php

class viewConverter_admin_registration_Summary extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Group Summary';
	}
	
	protected function body() {
		$body = parent::body();

		$detailsLink = '';
		if($this->showDetailsLink) {
			$detailsLink = <<<_
				{$this->HTML->link(array(
					'label' => 'Details',
					'href' => '/admin/registration/Registration',
					'parameters' => array(
						'eventId' => $this->event['id'],
						'id' => $this->group['id']
					)
				))}
				&nbsp;&nbsp;
_;
		}
		
		$f = new fragment_registration_summary_Summary($this->event, $this->group);
		
		$body .= <<<_
			<div id="content">
				<h3>{$this->title}</h3>
				
				<div>
					{$detailsLink}
					
					{$this->HTML->link(array(
						'label' => 'Print PDF',
						'href' => '/admin/registration/Summary',
						'parameters' => array(
							'a' => 'printPdf',
							'eventId' => $this->event['id'],
							'id' => $this->group['id']
						)
					))}
				</div>
				
				<div class="sub-divider"></div>	
				
				{$f->html()}
			</div>
_;
		
		return $body;
	}
	
	public function printPdf($properties) {
		$this->setProperties($properties);
		
		require_once 'config/lang/eng.php';
		require_once 'tcpdf.php';
		
		$pdf = new TCPDF(
			/*orientation*/ 'P',
			/*units*/       'in',
			/*page size*/	'A4',
			/*unicode*/		TRUE,
			/*encoding*/	'UTF-8',
			/*diskcache*/	FALSE
		);
		
		$pdf->SetCreator($this->event['code']);
		$pdf->SetAuthor($this->event['code']);
		$pdf->SetTitle("{$this->event['code']} Registration Group Summary");
		$pdf->SetSubject("{$this->event['code']} Registration Group Summary");
		
		$pdf->setPrintHeader(FALSE);
		$pdf->setPrintFooter(FALSE);
		
		$pdf->AddPage();
		
		$f = new fragment_registration_summary_Summary($this->event, $this->group, TRUE);
		$html = $f->html();
		
		// remove emtpy tables that cause TCPDF errors.
		$html = preg_replace('/(\n|\r)/', '', $html);
		$html = preg_replace('/<table style=".*?">\s*<\/table>/', '', $html);
		
		$pdf->writeHTML($html);
		
		return new template_TcpdfWrapper(
			$pdf, 
			'Group_Summary_'.time().'.pdf', 
			'I'
		);
	}
}

?>