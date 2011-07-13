<?php

class template_TcpdfWrapper extends template_Template
{
	function __construct($pdf, $name = 'file.pdf', $mode = 'I') {
		parent::__construct();

		$this->pdf = $pdf;
		$this->mode = $mode;
		$this->name = $name;
	}
	
	public function html() {
		$this->pdf->Output($this->name, $this->mode);
		
		return '';
	}
}
?>