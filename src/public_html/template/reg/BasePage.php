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
	
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/reg.less'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/shared.less'))}
			
			{$this->HTML->script(array('src' => '/js/less.js'))}
			
			{$this->HTML->script(array('src' => '/js/dojo/reggie_reg.js'))}
			
			<style type="text/css">
				body {
					background-color: #{$this->escapeHtml($this->event['appearance']['backgroundColor'])};
				}
				
				#header {
					background-color: #{$this->escapeHtml($this->event['appearance']['headerBackgroundColor'])};
				}
				
				#footer {
					background-color: #{$this->escapeHtml($this->event['appearance']['footerBackgroundColor'])};
				}
				
				.menu {
					background-color: #{$this->escapeHtml($this->event['appearance']['menuBackgroundColor'])};
				}
				
				.menu .current {
					background-color: #{$this->escapeHtml($this->event['appearance']['menuHighlightColor'])};
				}
				
				.menu-title {
					background-color: #{$this->escapeHtml($this->event['appearance']['menuTitleBackgroundColor'])};
				}
				
				td.reg-form {
					background-color: #{$this->escapeHtml($this->event['appearance']['pageBackgroundColor'])};
				}
				
				.reg-form-content {
					background-color: #{$this->escapeHtml($this->event['appearance']['formBackgroundColor'])};
				}
				
				.button {
					color: #{$this->escapeHtml($this->event['appearance']['buttonTextColor'])};
					background-color: #{$this->escapeHtml($this->event['appearance']['buttonBackgroundColor'])};
				}
			</style>	
_;
	}
	
	protected function body() {
		// category code for the form's post action.
		$category = model_reg_Session::getCategory();
		$cat = model_Category::code($category);
		
		if($this->showMenu) {
			$menu = new fragment_reg_Menu($this->event, $this->id);
			$menu = "<td>{$menu->html()}</td>";
		}
		else {
			$menu = '';
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
					{$this->HTML->hidden(array(
						'name' => 'pageId',
						'value' => $this->id
					))}
					
					<table><tr><td class="reg-form">
						<table class="reg-content"><tr>
							{$menu}
							
							<td class="reg-form-content">
								<div class="reg-form-title">{$this->title}</div>
								
								{$this->page->html()}
								
								<div class="divider"></div>
								
								{$this->getFormControls()}
							</td>
						</tr></table>
					</td></tr></table>
					
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
		$category = model_reg_Session::getCategory();
		$cat = model_Category::code($category);
		
		$visiblePages = model_EventPage::getVisiblePages($this->event, $category);

		$disabled = empty($visiblePages) || $visiblePages[0]['id'] === $this->id;
		$disabledAttr = $disabled? 'disabled="disabled"' : '';
		$disabledStyle = $disabled? 'color:lightgray;' : '';
		$onclick = $disabled? '' : "document.location=hhreg.util.contextUrl('/event/{$this->event['code']}/{$cat}?a=Previous&pageId={$this->id}');";
		
		return <<<_
			<input type="button" id="prev-button" class="button" style="{$disabledStyle}" value="Previous"
			       {$disabledAttr} onclick="{$onclick}" >
_;
	}
	
	private function getNextButton() {
		if($this->id === model_reg_RegistrationPage::$SUMMARY_PAGE_ID) {
			return '<input type="submit" id="next-button" class="button" name="a" value="Submit">';
		}
		else {
			return '<input type="submit" id="next-button" class="button" name="a" value="Next">';
		}
	}
}

?>