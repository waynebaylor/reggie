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
				<div>
					{$this->HTML->link(array(
						'label' => 'Details',
						'href' => '/admin/registration/Registration',
						'parameters' => array(
							'eventId' => $this->event['id'],
							'groupId' => $this->group['id'],
							'reportId' => $this->report['id']
						)
					))}
				</div>
				
				<div class="sub-divider"></div>		
_;
		}
		
		$f = new fragment_registration_summary_Summary($this->event, $this->group);
		
		$body .= <<<_
			<div id="content">
				<h3>{$this->title}</h3>
				
				{$detailsLink}
				
				{$f->html()}
			</div>
_;
		
		return $body;
	}
}

?>