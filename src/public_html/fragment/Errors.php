<?php 

class template_Errors extends template_Template
{
	private $msgs;
	
	function __construct(/*array*/ $errorMessages) {
		$this->msgs = $errorMessages;	
	}
	
	public function html() {
		$html = '';
		
		foreach ($this->msgs as $msg) {
			$html .= '<li>'.$msg.'</li>';
		}
		
		return '<ul class="error-messages">'.$html.'</ul>';
	}
}

?>