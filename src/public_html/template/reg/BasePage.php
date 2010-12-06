<?php

class template_reg_BasePage extends template_Page
{
	protected $errors;
	protected $event;
	protected $page;
	protected $title;
	protected $id;
	
	function __construct($config) {
		parent::__construct();
		
		$this->errors = isset($config['errors'])? $config['errors'] : array();
		$this->event = $config['event'];
		$this->page = $config['page'];
		$this->title = $config['title'];
		$this->id = $config['id'];	
		$this->showMenu = isset($config['showMenu'])? $config['showMenu'] : true;
		$this->showControls = isset($config['showControls'])? $config['showControls'] : true;
	}	
	
	protected function head() {
		return <<<_
			<title>{$this->event['displayName']} - {$this->title}</title>
	
			{$this->HTML->css(array('href' => '/js/dojo/resources/dojo.css'))}
			{$this->HTML->css(array('href' => '/js/dijit/themes/dijit.css'))}
			{$this->HTML->css(array('href' => '/js/dijit/themes/tundra/tundra.css'))}
			
			{$this->HTML->css(array('href' => '/css/reg.css'))}
		
			{$this->HTML->script(array('src' => '/js/dojo/dojo.js'))}
			
			<script type="text/javascript">
				dojo.registerModulePath("hhreg", "{$this->contextUrl('/js/hhreg')}");
				dojo.require("hhreg");
				
				dojo.addOnLoad(function() {
					dojo.require("hhreg.validation");
					
					var messages = dojo.byId("xhr-response");
					hhreg.validation.showMessages(dojo.fromJson(messages.value), messages.form);
				});
			</script>
			
			<style type="text/css">
				body {
					background-color: #{$this->escapeHtml($this->event['appearance']['backgroundColor'])}
				}
				
				#header {
					background-color: #{$this->escapeHtml($this->event['appearance']['headerColor'])}
				}
				
				#footer {
					background-color: #{$this->escapeHtml($this->event['appearance']['footerColor'])}
				}
				
				.menu {
					background-color: #{$this->escapeHtml($this->event['appearance']['menuColor'])}
				}
				
				.reg-form-content {
					background-color: #{$this->escapeHtml($this->event['appearance']['formColor'])}
				}
				
				.button {
					background-color: #{$this->escapeHtml($this->event['appearance']['buttonColor'])}
				}
			</style>	
_;
	}
	
	protected function body() {
		// category code for the form's post action.
		$category = model_RegSession::getCategory();
		$cat = model_Category::code($category);
		
		if($this->showMenu) {
			$menu = new fragment_reg_Menu($this->event, $this->id);
		}
		else {
			$menu = new fragment_Empty();
		}
		
		$errorMessages = new fragment_validation_ValidationErrors($this->errors);
		
		$footerContent = $this->event['appearance']['footerContent'];
		if(empty($footerContent)) {
			$footer = '';
		}
		else {
			$footer = <<<_
				<div id="footer">
					{$footerContent}
				</div>
_;
		}
		
		return <<<_
			<div id="header">
				{$this->event['appearance']['headerContent']}
			</div>
			<div id="content">	
				<form method="post" action="{$this->contextUrl("/event/{$this->event['code']}/{$cat}")}">
					<input type="hidden" name="pageId" value="{$this->id}"/>
					
					<table class="reg-content"><tr>
						<td>
							{$menu->html()}
						</td>
						<td class="reg-form-content">
							<div class="reg-form-title">{$this->title}</div>
							
							{$this->page->html()}
							
							{$this->getFormControls()}
						</td>
					</tr></table>
					
					{$errorMessages->html()}
				</form>
			</div>
			
			{$footer}
_;
	}
	
	private function getFormControls() {
		if($this->showControls) {
			$hasErrors = empty($this->errors)? 'hide' : 'validation-icon';
			
			return <<<_
				<div class="form-controls">
					{$this->getPreviousButton()}
					{$this->getNextButton()}
					
					<div class="{$hasErrors}">
						{$this->HTML->img(array(
							'src' => '/images/caution_red.gif',
							'alt' => 'Validation Errors',
							'title' => 'Validation Errors'
						))}
						<span class="error-text">Please correct the above errors.</span>
					</div>
				</div>		
_;
		}	
		
		return '';
	}
	
	private function getPreviousButton() {
		$category = model_RegSession::getCategory();
		$visiblePages = model_EventPage::getVisiblePages($this->event, $category);

		$disabled = empty($visiblePages) || $visiblePages[0]['id'] === $this->id;
		$disabledAttr = $disabled? 'disabled="disabled"' : '';
		$disabledClass = $disabled? 'disabled-button' : '';
		
		return <<<_
			<input type="submit" id="prev-button" class="button {$disabledClass}" name="a" value="Previous" {$disabledAttr}/>
_;
	}
	
	private function getNextButton() {
		if($this->id === model_RegistrationPage::$SUMMARY_PAGE_ID) {
			return '<input type="submit" id="next-button" class="button" name="a" value="Submit"/>';
		}
		else {
			return '<input type="submit" id="next-button" class="button" name="a" value="Next"/>';
		}
	}
}

?>