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
				<h3>Edit Registrations</h3>
				
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
					"<tr>
						<td></td>
						<td>
						{$this->HTML->hidden(array(
							'name' => 'registrationId',
							'value' => $registration['id']
						))}
						
						{$this->HTML->hidden(array(
							'name' => 'sectionId',
							'value' => $section['id']
						))}
						
						{$html}
						</td>
					</tr>"
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
		$html = $this->getSelectedRegOptionHtml($section, $registration);
		
		$html .= '<div class="divider"></div>';
		
		$html .= $this->getCancelledRegOptionHtml($section, $registration);
		
		return $html;
	}
	
	private function getSelectedRegOptionHtml($section, $registration) {
		$html = '';
		
		foreach($section['content'] as $group) {
			$html .= $this->getRegOptionGroupHtml($group, $registration);
		}
		
		return <<<_
			<table style="border-collapse:separate; border-spacing:20px 10px;">
				{$html}
			</table>
_;
	}
	
	private function getCancelledRegOptionHtml($section, $registration) {
		// FIXME show cancelled reg options in gray text.
	}
	
	private function getRegOptionGroupHtml($group, $registration) {
		$html = '';
		
		foreach($group['options'] as $opt) {
			$html .= $this->getRegOptionRow($registration, $opt);

			foreach($opt['groups'] as $optGroup) {
				$html .= $this->getRegOptionGroupHtml($optGroup, $registration);
			}	
		}
		
		return $html;
	}

	private function getVarQuantityHtml($section, $registration) {

	}

	/**
	 * if the registration selected the given option AND the selection has not been cancelled, then a row is returned.
	 */
	private function getRegOptionRow($registration, $regOption) {
		foreach($registration['regOptions'] as $o) {
			if($o['regOptionId'] == $regOption['id'] && empty($o['dateCancelled'])) {
				$price = db_RegOptionPriceManager::getInstance()->find($o['priceId']);
				$priceDisplay = '$'.number_format($price['price'], 2);
				
				return <<<_
					<tr>
						<td style="vertical-align:top;">{$regOption['description']}</td>
						<td style="text-align:right; vertical-align:top;">{$priceDisplay}</td>
						<td style="vertical-align:top;">
							{$this->HTML->link(array(
								'label' => 'Cancel',
								'href' => '/admin/registration/Registration',
								'parameters' => array(
									'a' => 'cancelRegOption',
									'id' => $o['id']
								)
							))}
						</td>
					</tr>
_;
			}
		}
		
		return '';
	}
}

?>