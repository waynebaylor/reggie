<?php

class fragment_reg_summary_RegType extends template_Template
{
	private $event;
	private $index;
	
	function __construct($event, $index) {
		parent::__construct();
		
		$this->event = $event;
		$this->index = $index;
	}
	
	public function html() {
		$regTypeId = model_reg_Session::getRegType($this->index);
		
		// if the event doesn't have a reg type section, then don't 
		// show anything for reg type.
		if(empty($regTypeId)) {
			return '';
		}
		
		$regType = db_RegTypeManager::getInstance()->find($regTypeId);
		
		return <<<_
			<tr>
				<td class="label">Registration Type</td>
				<td class="details">
					{$regType['description']}
				</td>
			</tr>
_;
	}
}

?>