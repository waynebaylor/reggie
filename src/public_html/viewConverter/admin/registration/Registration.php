<?php

class viewConverter_admin_registration_Registration extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getDeleteRegistration($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect("/admin/registration/Registration?eventId={$this->eventId}&groupId={$this->regGroupId}&reportId={$this->reportId}");
	}
	
	public function getPaymentSummary($properties) {
		$this->setProperties($properties);
		
		$frag = <<<_
			<tr>
				<td>Total Cost</td>
				<td>{$this->cost}</td>
			</tr>
			<tr>
				<td>Amount Tendered</td>
				<td>{$this->paid}</td>
			</tr>
			<tr>
				<td>Balance Due</td>
				<td>{$this->remainingBalance}</td>
			</tr>	
_;

		return new template_TemplateWrapper($frag);
	}
	
	public function getAddRegistrantToGroup($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect("/admin/registration/Registration?groupId={$this->groupId}&reportId={$this->reportId}#registrant{$this->newNumber}");		
	}
	
	public function getCancelRegistration($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect("/admin/registration/Registration?eventId={$this->eventId}&groupId={$this->regGroupId}&reportId={$this->reportId}#registrant{$this->registrantNumber}");
	}
}

?>