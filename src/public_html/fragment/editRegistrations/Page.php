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
		
		$html = <<<_
			<table style="border-collapse:separate; border-spacing:20px 10px;">
				{$html}
			</table>
_;

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
		
		return $form->html();
	}
	
	private function getVarQuantityRow($option, $registration) {
		$value = 0;
		$comments = '';
		$priceId = 0;
		
		foreach($registration['variableQuantity'] as $varQuantity) {
			if($option['id'] == $varQuantity['variableQuantityId']) {
				$value = $varQuantity['quantity'];
				$comments = $this->escapeHtml($varQuantity['comments']);
				$priceId = $varQuantity['priceId'];
			}
		}
		
		return <<<_
			<tr>
				<td style="vertical-align:top;">{$option['description']}</td>
				<td style="vertical-align:top; text-align:right;">
					{$this->HTML->text(array(
						'name' => model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'],
						'value' => $value,
						'size' => 2
					))}
					&nbsp;&#64;
					{$this->getVarQuantityPrice($option, $priceId)}
				</td>
				<td style="vertical-align:top;">
					{$comments}
				</td>
			</tr>
_;
	}
	
	private function getVarQuantityPrice($option, $priceId) {
		$prices = db_RegOptionPriceManager::getInstance()->findByVariableQuantityOption(array('id' => $option['id']));
		
		$dropDownOpts = array();
		foreach($prices as $price) {
			$dropDownOpts[] = array(
				'label' => '$'.number_format($price['price'], 2).' ('.$price['description'].')',
				'value' => $price['id']
			);
		}
		
		return $this->HTML->select(array(
			'name' => 'priceId_'.$option['id'],
			'value' => $priceId,
			'items' => $dropDownOpts
		));
	}
}

?>