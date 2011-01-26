<?php

class fragment_payment_Check extends template_Template
{
	private $eventPaymentType;
	private $values;
	private $selectedPaymentTypeId;
	
	function __construct($eventPaymentType, $values, $selectedPaymentTypeId) {
		parent::__construct();
		
		$this->eventPaymentType = $eventPaymentType;
		$this->values = $values;
		$this->selectedPaymentTypeId = $selectedPaymentTypeId;
	}
	
	public function html() {
		$showForm = 'hide';
		if(model_PaymentType::$CHECK === $this->selectedPaymentTypeId) {
			$showForm = '';
		}
		
		$instructions = ArrayUtil::getValue($this->eventPaymentType, 'instructions', '');
		
		return  <<<_
			<div class="check-payment-instructions {$showForm}">
				<div>{$instructions}</div>

				<div class="sub-divider"></div>
				
				<div>
					Check Number
					{$this->HTML->text(array(
						'name' => 'checkNumber',
						'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'checkNumber', '')),
						'size' => '10'
					))}
				</div>
			</div>
_;
	}
}

?>