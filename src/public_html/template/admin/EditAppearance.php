<?php

class template_admin_EditAppearance extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Edit Event Appearance');
		
		$this->event = $event;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'Appearance',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code']
		));	
	}
	
	protected function getContent() {
		$form = new fragment_XhrTableForm(
			'/admin/event/EditAppearance', 
			'saveAppearance', 
			$this->getFormRows()
		);
			
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
						'rows' => 10,
						'cols' => 75
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Page Footer</td>
				<td>
					{$this->HTML->textarea(array(
						'name' => 'footerContent',
						'value' => $this->escapeHtml($appearance['footerContent']),
						'rows' => 10,
						'cols' => 75
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Menu Title</td>
				<td>
					{$this->HTML->textarea(array(
						'name' => 'menuTitle',
						'value' => $this->escapeHtml($appearance['menuTitle']),
						'rows' => 10,
						'cols' => 75
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Header Background Color</td>
				<td>
					{$this->getColorInputs('headerBackgroundColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Background Color</td>
				<td>
					{$this->getColorInputs('backgroundColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Page Background Color</td>
				<td>
					{$this->getColorInputs('pageBackgroundColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Menu Title Background Color</td>
				<td>
					{$this->getColorInputs('menuTitleBackgroundColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Menu Background Color</td>
				<td>
					{$this->getColorInputs('menuBackgroundColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Menu Highlight Color</td>
				<td>
					{$this->getColorInputs('menuHighlightColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Form Background Color</td>
				<td>
					{$this->getColorInputs('formBackgroundColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Footer Background Color</td>
				<td>
					{$this->getColorInputs('footerBackgroundColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Button Text Color</td>
				<td>
					{$this->getColorInputs('buttonTextColor')}
				</td>
			</tr>
			<tr>
				<td class="label">Button Background Color</td>
				<td>
					{$this->getColorInputs('buttonBackgroundColor')}
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