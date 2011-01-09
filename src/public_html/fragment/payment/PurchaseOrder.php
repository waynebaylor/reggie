<?php

class fragment_payment_PurchaseOrder extends template_Template
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
		if(model_PaymentType::$PO === $this->selectedPaymentTypeId) {
			$showForm = '';
		}
		
		return <<<_
			<div class="po-payment-instructions {$showForm}">
				<div>{$this->eventPaymentType['instructions']}</div>

				<div class="sub-divider"></div>
				
				<div>
					PO Number
					{$this->HTML->text(array(
						'name' => 'purchaseOrderNumber',
						'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'purchaseOrderNumber', '')),
						'size' => '10'
					))}
				</div>
			</div>
_;
	}
}

?>