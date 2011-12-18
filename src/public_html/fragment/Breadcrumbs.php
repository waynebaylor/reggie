<?php

class fragment_Breadcrumbs extends template_Template
{
	function __construct($params = array()) {
		parent::__construct();
		
		$this->params = $params;
	}
	
	public function html() {
		$html = '';
		
		if(isset($this->params['eventId'])) {
			$html .= $this->HTML->hidden(array(
				'class' => 'breadcrumb',
				'name' => 'Event',
				'value' => "/admin/event/EditEvent?eventId={$this->params['eventId']}"
			));
		}
		
		if(isset($this->params['pageId'])) {
			$html .= $this->HTML->hidden(array(
				'class' => 'breadcrumb',
				'name' => 'Page',
				'value' => "/admin/page/Page?eventId={$this->params['eventId']}&id={$this->params['pageId']}"
			));
		}
		
		if(isset($this->params['sectionId'])) {
			$html .= $this->HTML->hidden(array(
				'class' => 'breadcrumb',
				'name' => 'Section',
				'value' => "/admin/section/Section?eventId={$this->params['eventId']}&id={$this->params['sectionId']}"
			));
		}
		
		return $html;
	}
}

?>