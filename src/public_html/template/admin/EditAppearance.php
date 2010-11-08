<?php

class template_admin_EditAppearance extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Edit Event Appearance');
		
		$this->event = $event;
	}
	
	protected function getContent() {
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'Appearance',
			'event' => $this->event
		));
		
		$form = new fragment_XhrTableForm(
			'/action/admin/event/EditAppearance', 
			'saveAppearance', 
			$this->getFormRows());
			
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				
				dojo.addOnLoad(function() {
					dojo.query("input.color-value").forEach(function(input) {
						var display = dojo.query(".color-display", input.parentNode)[0];
						
						dojo.connect(input, "onblur", function() {
							dojo.style(display, "backgroundColor", "#"+input.value);	
						});
					});
				});
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>Event Appearance</h3>

					{$form->html()}
				</div>
				
				<div class="divider"></div>
				
				{$breadcrumbs->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		$appearance = $this->event['appearance'];

		return <<<_
			<tr>
				<td class="label">Page Header</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $appearance['id']
					))}
					
					{$this->HTML->textarea(array(
						'name' => 'headerContent',
						'value' => $this->escapeHtml($appearance['headerContent']),
						'rows' => '5',
						'cols' => '50'
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Page Footer</td>
				<td>
					{$this->HTML->textarea(array(
						'name' => 'footerContent',
						'value' => $this->escapeHtml($appearance['footerContent']),
						'rows' => '5',
						'cols' => '50'
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Background Color</td>
				<td>
					{$this->getColorInputs('backgroundColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Header Color</td>
				<td>
					{$this->getColorInputs('headerColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Footer Color</td>
				<td>
					{$this->getColorInputs('footerColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Menu Color</td>
				<td>
					{$this->getColorInputs('menuColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Form Color</td>
				<td>
					{$this->getColorInputs('formColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Button Color</td>
				<td>
					{$this->getColorInputs('buttonColor')}
				</td>
			</tr>
		
_;
	}
	
	private function getColorInputs($name) {
		$appearance = $this->event['appearance'];
		
		return <<<_
			#{$this->HTML->text(array(
				'class' => 'color-value',
				'name' => $name,
				'value' => $this->escapeHtml($appearance[$name]),
				'size' => '6',
				'maxlength' => '6'
			))}
			
			{$this->HTML->text(array(
				'class' => 'color-display',
				'size' => '4',
				'disabled' => 'disabled',
				'value' => '',
				'style' => "background-color: #{$this->escapeHtml($appearance[$name])}"
			))}	
_;
	}
}

?>