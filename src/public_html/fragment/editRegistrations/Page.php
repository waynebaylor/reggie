<?php

class fragment_editRegistrations_Page extends template_Template
{
	private $page;
	private $group;
	private $registration;
	
	function __construct($page, $group, $registration) {
		parent::__construct();
		
		$this->page = $page;
		$this->group = $group;
		$this->registration = $registration;
	}
	
	public function html() {
		$html = "<h3>{$this->page['title']}</h3>";
			
		$sections = $this->page['sections'];
		foreach($sections as $section) {
			if(model_Section::containsRegTypes($section)) {
				$html .= $this->getRegTypeHtml($section, $this->registration);
			}
			else if(model_Section::containsContactFields($section)) {
				$fragment = new fragment_editRegistrations_InformationFields($section, $this->registration);
				$html .= $fragment->html();
			}
			else if(model_Section::containsRegOptions($section)) {
				$fragment = new fragment_editRegistrations_RegOptions($section, $this->group, $this->registration);
				$html .= $fragment->html();
			}
			else if(model_Section::containsVariableQuantityOptions($section)) {
				$html .= $this->getVarQuantityHtml($section, $this->registration);
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
	
	private function getVarQuantityHtml($section, $registration) {
		$html = '';
		
		foreach($section['content'] as $option) {
			$html .= $this->getVarQuantityRow($option, $registration);
		}
		
		return <<<_
			<table style="border-collapse:separate; border-spacing:20px 10px;">{$html}</table>
_;
	}
	
	private function getVarQuantityRow($option, $registration) {
		$html = '';
		
		foreach($registration['variableQuantity'] as $varQuantity) {
			if($option['id'] == $varQuantity['variableQuantityId']) {
				$price = db_RegOptionPriceManager::getInstance()->find($varQuantity['priceId']);
				//display like: @ $45.95
				$priceDisplay = ' &#64; $'.number_format($price['price'], 2);
				
				$html .= <<<_
					<tr>
						<td style="vertical-align:top;">{$option['description']}</td>
						<td style="vertical-align:top; text-align:right;">
							{$this->HTML->text(array(
								'name' => model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'],
								'value' => $varQuantity['quantity'],
								'size' => 2
							))}
						</td>
						<td style="vertical-align:top;">
							{$this->escapeHtml($varQuantity['comments'])}
						</td>
					</tr>
_;
			}
		}
		
		return $html;
	}
}

?>