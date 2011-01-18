<?php

class fragment_editRegistrations_RegOptions extends template_Template
{
	private $event;
	private $report;
	private $registration;
	
	function __construct($event, $report, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->report = $report;
		$this->registration = $registration;
	}
	
	public function html() {
		$list = new fragment_editRegistrations_regOption_List($this->event, $this->report, $this->registration);
		$add = new fragment_editRegistrations_regOption_Add($this->event, $this->report, $this->registration);
		
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
		// don't show an empty form if there are no var options.
		$varOpts = model_Event::getVariableQuantityOptions($this->event);
		if(empty($varOpts)) {
			return '';	
		}
		
		$html = '';
		
		foreach($varOpts as $option) {
			$html .= $this->getVarQuantityRow($option, $this->registration);
		}
		
		$html = <<<_
			<table class="edit-var-reg-options">
				{$html}
			</table>
_;

		$form = new fragment_XhrTableForm(
			'/admin/registration/RegOption', 
			'saveVariableQuantity', 
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
		
		return <<<_
			<div class="fragment-edit">
				{$form->html()}
			</div>
_;
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
				<td class="label">{$option['description']}</td>
				<td class="quantity">
					{$this->HTML->text(array(
						'name' => model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'],
						'value' => $value,
						'size' => 2
					))}
					&nbsp;&#64;
				</td>
				<td class="price">
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