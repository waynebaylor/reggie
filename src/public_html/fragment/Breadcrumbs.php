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
		
		if(isset($this->params['regTypeId'])) {
			$html .= $this->HTML->hidden(array(
				'class' => 'breadcrumb',
				'name' => 'Registration Type',
				'value' => "/admin/regType/RegType?eventId={$this->params['eventId']}&id={$this->params['regTypeId']}"
			));
		}
		
		if(isset($this->params['contactFieldId'])) {
			$html .= $this->HTML->hidden(array(
				'class' => 'breadcrumb',
				'name' => 'Information Field',
				'value' => "/admin/contactField/ContactField?eventId={$this->params['eventId']}&id={$this->params['contactFieldId']}"
			));
		}
		
		// the option groups and reg options are ordered in a sub-array according to their hierarchy.
		if(isset($this->params['regGroupsAndOpts'])) {
			foreach($this->params['regGroupsAndOpts'] as $index => $groupOrOptId) {
				// the first will always be the option group under the section.
				if($index === 0) {
					$html .= $this->HTML->hidden(array(
						'class' => 'breadcrumb',
						'name' => 'Option Group',
						'value' => "/admin/regOption/SectionRegOptionGroup?eventId={$this->params['eventId']}&id={$groupOrOptId}"
					));
				}
				
				// reg option comes next.
				if($index > 0 && $index % 2 === 1) {
					$html .= $this->HTML->hidden(array(
						'class' => 'breadcrumb',
						'name' => 'Reg Option',
						'value' => "/admin/regOption/RegOption?eventId={$this->params['eventId']}&id={$groupOrOptId}"
					));
				}
				// followed by option group.
				else if($index > 0 && $index % 2 === 0) {
					$html .= $this->HTML->hidden(array(
						'class' => 'breadcrumb',
						'name' => 'Option Group',
						'value' => "/admin/regOption/RegOptionGroup?eventId={$this->params['eventId']}&id={$groupOrOptId}"
					));
				}
			}
		}
		
		if(isset($this->params['variableQuantityOptionId'])) {
			$html .= $this->HTML->hidden(array(
				'class' => 'breadcrumb',
				'name' => 'Variable Quantity Option',
				'value' => "/admin/regOption/VariableQuantity?eventId={$this->params['eventId']}&id={$this->params['variableQuantityOptionId']}"
			));
		}
		
		if(isset($this->params['regOptionPriceId'])) {
			$html .= $this->HTML->hidden(array(
				'class' => 'breadcrumb',
				'name' => 'Reg Option Price',
				'value' => "/admin/regOption/RegOptionPrice?eventId={$this->params['eventId']}&id={$this->params['regOptionPriceId']}"
			));
		}
		
		return $html;
	}
}

?>