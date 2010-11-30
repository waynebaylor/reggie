<?php

class template_admin_EmailTemplate extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Email Template');
		
		$this->event = $event;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'EmailTemplate',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code']
		));
	}
	
	protected function getContent() {
		$form = new fragment_XhrTableForm(
			'/action/admin/email/EmailTemplate',
			'saveTemplate',
			$this->getFormRows());
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.emailTemplate");
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>Email Template</h3>
				
					{$form->html()}
				</div>

				<div class="divider"></div>
				
				<div id="email-test">
					<form method="post" action="/action/admin/email/EmailTemplate">
						{$this->HTML->hidden(array(
							'name' => 'id',
							'value' => $this->event['emailTemplate']['id']
						))}
						
						{$this->HTML->hidden(array(
							'name' => 'action',
							'value' => 'sendTest'
						))}
						
						Send Test Email <input type="text" name="to"/> 
						<input type="submit" class="button" value="Send"/>
					</form>
				</div>
			</div>
_;
	}
	
	private function getFormRows() {
		$template = $this->event['emailTemplate'];
		$infoClass = $template['enabled'] === 'true'? '' : 'hide';
		
		return <<<_
			<tr>
				<td class="label">
					Status
				</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $template['id']
					))}
					
					{$this->HTML->radios(array(
						'name' => 'enabled',
						'value' => $this->escapeHtml($template['enabled']),
						'items' => array(
							array(
								'id' => 'enabled_true',
								'label' => 'Enabled',
								'value' => 'true'
							),
							array(
								'id' => 'enabled_false',
								'label' => 'Disabled',
								'value' => 'false'
							)
						)
					))}
				</td>
			</tr>
			<tr class="template-info {$infoClass}">
				<td class="label">From Address</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'fromAddress',
						'value' => $this->escapeHtml($template['fromAddress'])
					))}
				</td>
			</tr>
			<tr class="template-info {$infoClass}">
				<td class="label">Bcc Address</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'bcc',
						'value' => $this->escapeHtml($template['bcc'])
					))}
				</td>
			</tr>
			<tr class="template-info {$infoClass}">
				<td class="label">Subject</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'subject',
						'value' => $this->escapeHtml($template['subject'])
					))}
				</td>
			</tr>
			<tr class="template-info {$infoClass}">
				<td class="label">Before Summary</td>
				<td>
					{$this->HTML->textarea(array(
						'name' => 'header',
						'value' => $this->escapeHtml($template['header']),
						'rows' => 5,
						'cols' => 50
					))}
				</td>
			</tr>
			<tr class="template-info {$infoClass}">
				<td></td>
				<td>[Registration Summary]</td>
			</tr>
			<tr class="template-info {$infoClass}">
				<td class="label">After Summary</td>
				<td>
					{$this->HTML->textarea(array(
						'name' => 'footer',
						'value' => $this->escapeHtml($template['footer']),
						'rows' => 5,
						'cols' => 50
					))}
				</td>
			</tr>
_;
	}
}

?>