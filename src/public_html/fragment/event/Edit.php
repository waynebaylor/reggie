<?php

class fragment_event_Edit extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;	
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/action/admin/event/EditEvent', 
			'saveEvent', 
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-edit">
				<h3>Edit Event</h3>
				{$this->getEventLinks()}
				{$form->html()}
			</div>
_;
	}
	
	private function getEventLinks() {
		return <<<_
			{$this->HTML->link(array(
				'label' => 'Appearance',
				'href' => '/action/admin/event/EditAppearance',
				'title' => 'Edit event appearance',
				'parameters' => array(
					'action' => 'view',
					'eventId' => $this->event['id']
				)
			))}
			&nbsp;
			{$this->HTML->link(array(
				'label' => 'Payment Options',
				'href' => '/action/admin/event/EditPaymentOptions',
				'title' => 'Edit event payment options',
				'parameters' => array(
					'action' => 'view',
					'id' => $this->event['id']
				)
			))}
			&nbsp;
			{$this->HTML->link(array(
				'label' => 'Email Template',
				'href' => '/action/admin/email/EmailTemplate',
				'title' => 'Edit event email template',
				'parameters' => array(
					'action' => 'view',
					'id' => $this->event['id']
				)
			))}
			
			<div class="sub-divider"></div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Code</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->event['id']
					))}
					{$this->HTML->text(array(
						'name' => 'code',
						'value' => $this->escapeHtml($this->event['code']),
						'maxlength' => '255'
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Title</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'displayName',
						'value' => $this->escapeHtml($this->event['displayName']),
						'size' => '50',
						'maxlength' => '255'
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Reg Open</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'regOpen',
						'value' => $this->escapeHtml($this->event['regOpen']),
						'size' => '16',
						'maxlength' => '16'
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Reg Closed</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'regClosed',
						'value' => $this->escapeHtml($this->event['regClosed']),
						'size' => '16',
						'maxlength' => '16'
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Capacity</td>
				<td>
					<input type="text" size="5" name="capacity" value="{$this->escapeHtml($this->event['capacity'])}"/>
				</td>
			</tr>
			<tr>
				<td class="label">Reg Closed Text</td>
				<td class="admin_td">
					<textarea rows="5" cols="50" name="regClosedText">{$this->escapeHtml($this->event['regClosedText'])}</textarea>
				</td>
			</tr>
			<tr>
				<td class="label">Cancellation Policy</td>
				<td class="admin_td">
					<textarea rows="5" cols="50" name="cancellationPolicy">{$this->escapeHtml($this->event['cancellationPolicy'])}</textarea>
				</td>
			</tr>
_;
	}
}

?>