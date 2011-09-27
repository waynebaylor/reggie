<?php

class viewConverter_admin_event_EditAppearance extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$html = $this->getContent();
		return new template_TemplateWrapper($html);
	}
	
	public function getSaveAppearance($properties) {
		$this->setProperties($properties);
		
		return new fragment_Success();
	}
	
	private function getContent() {
		$form = new fragment_XhrTableForm(
			'/admin/event/EditAppearance', 
			'saveAppearance', 
			$this->getFormRows()
		);
			
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrTableForm");
				
				dojo.addOnLoad(function() {
					dojo.query("#edit-event-appearance form").forEach(function(item) {
						hhreg.xhrTableForm.bind(item);
					});
					
					dojo.query("#edit-event-appearance textarea.expanding").forEach(function(item) {
						hhreg.util.enhanceTextarea(item);
					});
					
					dojo.query("input.color-value").forEach(function(input) {
						var display = dojo.query(".color-display", input.parentNode)[0];
						
						dojo.connect(input, "onblur", function() {
							dojo.style(display, "backgroundColor", "#"+input.value);	
						});
					});
				});
			</script>
			
			<div id="edit-event-appearance">
				{$form->html()}
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
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->eventId
					))}
					
					{$this->HTML->textarea(array(
						'class' => 'expanding',
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
						'class' => 'expanding',
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
						'class' => 'expanding',
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