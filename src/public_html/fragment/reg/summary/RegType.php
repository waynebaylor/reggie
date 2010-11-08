<?php

class fragment_reg_summary_RegType extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$regTypeId = model_RegSession::getRegType();
		
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
			<tr>
				<td colspan="2">
					<div class="summary-divider"></div>
				</td>
			</tr>
_;
	}
}

?>