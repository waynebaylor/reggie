<?php

class viewConverter_admin_event_EditPaymentOptions extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$html = $this->getContent();
		return new template_TemplateWrapper($html);
	}
	
	public function getSavePaymentTypes($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	private function getContent() {
		$form = new fragment_XhrTableForm(
			'/admin/event/EditPaymentOptions',
			'savePaymentTypes',
			$this->getFormRows());
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.util");
				dojo.require("hhreg.xhrTableForm");
				dojo.require("hhreg.admin.paymentTypes");
				
				dojo.addOnLoad(function() {
					dojo.query("#edit-event-payment-options form").forEach(function(item) {
						hhreg.xhrTableForm.bind(item);
					});
					
					dojo.query("#edit-event-payment-options textarea.expanding").forEach(function(item) {
						hhreg.util.enhanceTextarea(item);
					});
				});
			</script>
			
			<div id="edit-event-payment-options">
				{$form->html()}
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
						'class' => 'expanding',
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
								'value' => $enabled? 'T' : 'F',
								'items' => array(
									array(
										'label' => 'Enabled',
										'value' => 'T'
									),
									array(
										'label' => 'Disabled',
										'value' => 'F'
									)
								)
							))}
						</td>
					</tr>
					<tr class="payment-type-details {$showDetails}">
						<td class="label">Instructions</td>
						<td>
							{$this->HTML->textarea(array(
								'class' => 'expanding',
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
								'value' => $enabled? 'T' : 'F',
								'items' => array(
									array(
										'label' => 'Enabled',
										'value' => 'T'
									),
									array(
										'label' => 'Disabled',
										'value' => 'F'
									)
								)
							))}
						</td>
					</tr>
					<tr class="payment-type-details {$showDetails}">
						<td class="label">Instructions</td>
						<td>
							{$this->HTML->textarea(array(
								'class' => 'expanding',
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
								'value' => $enabled? 'T' : 'F',
								'items' => array(
									array(
										'label' => 'Enabled',
										'value' => 'T'
									),
									array(
										'label' => 'Disabled',
										'value' => 'F'
									)
								)
							))}
						</td>
					</tr>
					<tr class="payment-type-details {$showDetails}">
						<td class="label">Instructions</td>
						<td>
							{$this->HTML->textarea(array(
								'class' => 'expanding',
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