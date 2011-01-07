<?php

class template_admin_EditPaymentOptions extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Edit Event Payment Options');
		
		$this->event = $event;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'PaymentOptions',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code']
		));
	}
	protected function getContent() {
		$form = new fragment_XhrTableForm(
			'/admin/event/EditPaymentOptions',
			'savePaymentTypes',
			$this->getFormRows());
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.paymentTypes");
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>Payment Options</h3>

					{$form->html()}
				</div>
			</div>
_;
	}
	
	private function getFormRows() {
		$html = <<<_
			<tr>
				<td class="label">General Instructions</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					
					{$this->HTML->textarea(array(
						'name' => 'paymentInstructions',
						'value' => $this->event['paymentInstructions'],
						'rows' => 10,
						'cols' => 75
					))}
				</td>
			</tr>
_;
		
		foreach(model_PaymentType::values() as $type) {
			$html .= <<<_
				<tr>
					<td colspan="2">
						<h4>{$type['displayName']}</h4>
						
						<table class="payment-type">
							{$this->getPaymentTypeForm($type)}
						</table>
						
						<div class="sub-divider"></div>
					</td>
				</tr>
_;
		}

		return $html;
	}
	
	private function getPaymentTypeForm($type) { 
		$enabled = model_Event::isPaymentTypeEnabled($this->event, $type); 
		$showDetails = $enabled? '' : 'hide';
		
		$details = model_Event::getPaymentTypeDirections($this->event, $type);
		
		switch($type['id']) {
			case model_PaymentType::$CHECK: 
				return <<<_
					<tr>
						<td class="label">Status</td>
						<td>
							{$this->HTML->radios(array(
								'name' => "paymentTypes_{$type['id']}_enabled",
								'value' => $enabled? 'true' : 'false',
								'items' => array(
									array(
										'label' => 'Enabled',
										'value' => 'true'
									),
									array(
										'label' => 'Disabled',
										'value' => 'false'
									)
								)
							))}
						</td>
					</tr>
					<tr class="payment-type-details {$showDetails}">
						<td class="label">Instructions</td>
						<td>
							{$this->HTML->textarea(array(
								'name' => "paymentTypes_{$type['id']}_instructions",
								'value' => $enabled? $this->escapeHtml($details['instructions']) : '',
								'rows' => 10,
								'cols' => 75
							))}
						</td>
					</tr>
_;
				break;
			case model_PaymentType::$PO:
				return <<<_
					<tr>
						<td class="label">Status</td>
						<td>
							{$this->HTML->radios(array(
								'name' => "paymentTypes_{$type['id']}_enabled",
								'value' => $enabled? 'true' : 'false',
								'items' => array(
									array(
										'label' => 'Enabled',
										'value' => 'true'
									),
									array(
										'label' => 'Disabled',
										'value' => 'false'
									)
								)
							))}
						</td>
					</tr>
					<tr class="payment-type-details {$showDetails}">
						<td class="label">Instructions</td>
						<td>
							{$this->HTML->textarea(array(
								'name' => "paymentTypes_{$type['id']}_instructions",
								'value' => $enabled? $this->escapeHtml($details['instructions']) : '',
								'rows' => 10,
								'cols' => 75
							))}
						</td>
					</tr>
_;
				break;
			case model_PaymentType::$AUTHORIZE_NET:
				return <<<_
					<tr>
						<td class="label">Status</td>
						<td>
							{$this->HTML->radios(array(
								'name' => "paymentTypes_{$type['id']}_enabled",
								'value' => $enabled? 'true' : 'false',
								'items' => array(
									array(
										'label' => 'Enabled',
										'value' => 'true'
									),
									array(
										'label' => 'Disabled',
										'value' => 'false'
									)
								)
							))}
						</td>
					</tr>
					<tr class="payment-type-details {$showDetails}">
						<td class="label">Instructions</td>
						<td>
							{$this->HTML->textarea(array(
								'name' => "paymentTypes_{$type['id']}_instructions",
								'value' => $enabled? $this->escapeHtml($details['instructions']) : '',
								'rows' => 10,
								'cols' => 75
							))}
						</td>
					</tr>
					<tr class="payment-type-details {$showDetails}">
						<td class="label">Login</td>
						<td>
							{$this->HTML->text(array(
								'name' => "paymentTypes_{$type['id']}_login",
								'value' => $enabled? $this->escapeHtml($details['login']) : '',
								'size' => '20'
							))}
						</td>
					</tr>
					<tr class="payment-type-details {$showDetails}">
						<td class="label">Transaction Key</td>
						<td>
							{$this->HTML->text(array(
								'name' => "paymentTypes_{$type['id']}_transactionKey",
								'value' => $enabled? $this->escapeHtml($details['transactionKey']) : '',
								'size' => '20'
							))}
						</td>
					</tr>
_;
				break;
		}
	}
}

?>