<?php 

class fragment_payment_PaymentChooser extends template_Template
{
	private $event;
	private $currentValues;
	private $enableAll;
	
	function __construct($event, $currentValues = array(), $enableAll = false) {
		parent::__construct();
		
		$this->event = $event;
		$this->currentValues = $currentValues;
		$this->enableAll = $enableAll;
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.reg.paymentTypes");
			</script>
			
			<table class="payment-types">
				<tr>
					<td class="tab-cell">{$this->getPaymentTypeTabs()}</td>
					<td class="form-cell">{$this->getPaymentTypeForms()}</td>
				</tr>
			</table>
_;
	}
	
	private function getPaymentTypeTabs() {
		$html = '<table class="payment-type-tabs">';
		
		$selectedTypeId = $this->getSelectedPaymentTypeId();
		
		foreach($this->getDisplayedPaymentTypes() as $type) {
			$typeId = intval($type['paymentTypeId'], 10);
			
			$selected = ($selectedTypeId === $typeId);
			
			if(model_PaymentType::$AUTHORIZE_NET === $typeId) {
				$type['displayName'] = 'Credit Card'; // We don't want attendees to see 'Authorize.NET'.
			}
			
			$tabSelected = $selected? 'selected-tab' : '';
			
			$html .= <<<_
				<tr>
					<td class="payment-type-tab {$tabSelected}">
						{$this->HTML->radio(array(
							'name' => 'paymentType',
							'value' => $type['paymentTypeId'],
							'label' => $type['displayName'],
							'checked' => $selected
						))}
					</td>
				</tr>
_;
		}
		
		return $html.'</table>';
	}
	
	private function getPaymentTypeForms() {
		$html = '';
		
		foreach($this->getDisplayedPaymentTypes() as $paymentType) {
			$html .= $this->getPaymentTypeForm($paymentType);
		}
		
		return <<<_
			<div class="payment-instructions">
				{$html}
				
				<div class="sub-divider"></div>
			</div>
_;
	}
	
	private function getPaymentTypeForm($type) {
		$html = '';
		
		switch($type['paymentTypeId']) {
			case model_PaymentType::$CHECK:
				$html .= $this->checkForm($type);
				break;
			case model_PaymentType::$PO:
				$html .= $this->purchaseOrderForm($type);
				break;
			case model_PaymentType::$AUTHORIZE_NET:
				$html .= $this->authorizeNetForm($type);
				break;
			default:
				throw new Exception('Invalid payment type: '.$type['paymentTypeId']);
		}

		return $html;
		
	}
	
	private function checkForm($type) {
		$check = new fragment_payment_Check($type, $this->currentValues, $this->getSelectedPaymentTypeId());
		return $check->html();
	}
	
	private function purchaseOrderForm($type) {
		$po = new fragment_payment_PurchaseOrder($type, $this->currentValues, $this->getSelectedPaymentTypeId());
		return $po->html();
	}
	
	private function authorizeNetForm($type) {
		$auth = new fragment_payment_AuthorizeNET($type, $this->currentValues, $this->getSelectedPaymentTypeId());
		return $auth->html();
	}
	
	private function getSelectedPaymentTypeId() {
		$paymentTypeFromSession = ArrayUtil::getValue($this->currentValues, 'paymentType', 0);
		
		$paymentTypes = $this->getDisplayedPaymentTypes();
		$firstPaymentType = reset($paymentTypes);
		$firstPaymentType = $firstPaymentType['paymentTypeId'];

		// if no payment type has been selected, then default to the first one.
		$id = empty($paymentTypeFromSession)? $firstPaymentType : $paymentTypeFromSession;
	
		return intval($id, 10);
	}
	
	private function getDisplayedPaymentTypes() {
		if($this->enableAll) {
			$paymentTypes = array();
			foreach(db_payment_PaymentTypeManager::getInstance()->findAll() as $pt) {
				// only show authorize.net if the event has it enabled. this is done because specific information 
				// is needed for it to work.
				if($pt['id'] != model_PaymentType::$AUTHORIZE_NET || model_Event::isPaymentTypeEnabled($this->event, $pt)) {
					$paymentTypes[] = array(
						'paymentTypeId' => $pt['id'],
						'displayName' => $pt['displayName'],
					);
				}
			}
		}
		else {
			$paymentTypes = $this->event['paymentTypes'];
		}
		
		return $paymentTypes;
	}
}

?>
