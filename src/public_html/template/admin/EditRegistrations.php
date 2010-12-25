<?php

class template_admin_EditRegistrations extends template_AdminPage
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct('Edit Registrations');
		
		$this->event = $event;
		$this->group = $group;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Empty();
	}
	
	protected function getContent() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			<div id="content">
				<h3>Edit Registration</h3>
				
				{$this->getRegistrants()}
			</div>
_;
	}
	
	private function getRegistrants() {
		$html = '';
		
		foreach($this->group['registrations'] as $r) {
			$html .= $this->getRegistration($r);
		}
		
		return $html;
	}
	
	private function getRegistration($registration) {
		$html = '';
		
		$pages = $this->event['pages'];
		foreach($pages as $page) {
			$html .= $this->getPageHtml($page, $registration);
			$html .= '<div class="sub-divider"></div>';
		}
			
		return $html;
	}
	
	private function getPageHtml($page, $registration) {
		$html = "<h3>{$page['title']}</h3>";
			
		$sections = $page['sections'];
		foreach($sections as $section) {
			if(model_Section::containsRegTypes($section)) {
				$html .= $this->getRegTypeHtml($section, $registration);
			}
			else if(model_Section::containsContactFields($section)) {
				$html .= $this->getInformationHtml($section, $registration);
				$form = new fragment_XhrTableForm(
					'/admin/registration/Registration', 
					'save', 
					"<tr><td></td><td>{$html}</td></tr>"
				);
				
				$html = $form->html();
			}
			else if(model_Section::containsRegOptions($section)) {
				$html .= $this->getRegOptionHtml($section, $registration);
			}
			else if(model_Section::containsVariableQuantityOptions($section)) {
				$html .= $this->getVarQuantityHtml($section, $registration);
			}
		}
			
		return <<<_
			<div class="fragment-edit">{$html}</div>
_;
	}

	private function getRegTypeHtml($section, $registration) {
		$regTypes = $section['content'];
		
		$html = '';
		
		foreach($regTypes as $regType) {
			if($registration['regTypeId'] === $regType['id']) {
				$html .= $regType['description'];
			}
		}
		
		return $html;
	}

	private function getInformationHtml($section, $registration) {
		$regTypeId = $registration['regTypeId'];
		$values = array();
		foreach($registration['information'] as $info) {
			$values[$info['contactFieldId']] = $info['value'];
		}
		
		$fragment = new fragment_reg_ContactFields($section, $regTypeId, $values);

		return $fragment->html();
	}

	private function getRegOptionHtml($section, $registration) {

	}

	private function getVarQuantityHtml($section, $registration) {

	}
}

?>