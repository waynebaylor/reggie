<?php

class fragment_editRegistrations_RegOptions extends template_Template
{
	private $event;
	private $report;
	private $regGroup;
	private $registration;
	
	function __construct($event, $report, $regGroup, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->report = $report;
		$this->regGroup = $regGroup;
		$this->registration = $registration;
	}
	
	public function html() {
		$list = new fragment_editRegistrations_regOption_List($this->event, $this->report, $this->regGroup, $this->registration);
		$add = new fragment_editRegistrations_regOption_Add($this->event, $this->registration);
		
		return <<<_
			<div class="registrant-details-section">
				<h3>Registration Options</h3>
				
				<div class="fragment-reg-options">
					<div>
						{$list->html()}
					</div>
	
					<div class="sub-divider"></div>
					
					{$add->html()}
				</div>
				
				<div class="divider"></div>
				
				{$this->getVariableOptions()}
			</div>
_;
	}
	
	private function getVariableOptions() {
		$html = '';

		$varOpts = model_Event::getVariableQuantityOptions($this->event);
		foreach($varOpts as $option) {
			$html .= $this->getVarQuantityRow($option, $this->registration);
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
						'value' => $this->registration['id']
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