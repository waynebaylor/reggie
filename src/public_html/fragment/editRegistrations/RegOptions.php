<?php

class fragment_editRegistrations_RegOptions extends template_Template
{
	private $event;
	private $registration;
	
	function __construct($event, $registration, $registrantNum = 1) {
		parent::__construct();
		
		$this->event = $event;
		$this->registration = $registration;
		$this->registrantNum = $registrantNum;
	}
	
	public function html() {
		$list = new fragment_editRegistrations_regOption_List($this->event, $this->registration, $this->registrantNum);
		$add = new fragment_editRegistrations_regOption_Add($this->event, $this->registration, $this->registrantNum);
		
		return <<<_
			<div id="registrant{$this->registration['id']}-registration_options" class="registrant-sub-tab">
				<span class="hide sub-tab-label">Registration Options</span>
				
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
						'name' => 'eventId',
						'value' => $this->registration['eventId']
					))}
					{$this->HTML->hidden(array(
						'name' => 'registrationId',
						'value' => $this->registration['id']
					))}
					
					{$html}
				</td>
			</tr>"
		);
		
		return <<<_
			<div class="fragment-edit var-quantity-options">
				{$form->html()}
			</div>
_;
	}
	
	private function getVarQuantityRow($option, $registration) {
		$value = 0;
		$comments = '';
		$priceId = 0;
		$lastModified = '';
		
		foreach($registration['variableQuantity'] as $varQuantity) {
			if($option['id'] == $varQuantity['variableQuantityId']) {
				$value = $varQuantity['quantity'];
				$priceId = $varQuantity['priceId'];
				$lastModified = "( Last Modified: {$varQuantity['lastModified']} )";
			}
		}
		
		return <<<_
			<tr>
				<td class="label">{$option['description']}</td>
				<td class="quantity" style="white-space:nowrap;">
					{$this->HTML->text(array(
						'name' => model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'],
						'value' => $value,
						'size' => 2
					))}
					&nbsp;&#64;
				</td>
				<td class="price">
					{$this->getVarQuantityPrice($option, $priceId)}
					{$lastModified}
				</td>
			</tr>
_;
	}
	
	private function getVarQuantityPrice($option, $priceId) {
		$prices = db_RegOptionPriceManager::getInstance()->findByVariableQuantityOption(array(
			'eventId' => $option['eventId'],
			'variableQuantityId' => $option['id']
		));
		
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