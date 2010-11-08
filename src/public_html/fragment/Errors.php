<?php 

require_once 'template/Template.php';

class template_Errors extends template_Template
{
	private $msgs;
	
	function __construct(/*array*/ $errorMessages) {
		$this->msgs = $errorMessages;	
	}
	
	public function html() {
		$html = '<ul class="error-messages">';
		
		foreach ($this->msgs as $msg) {
			$html .= '<li>'.$msg.'</li>';
		}
		
		$html .= '</ul>';
		return $html;
	}
}

?>