<?php

/**
 * 
 * A View Converter provides the presentation content in response to a user's action. This
 * base class implements a template for viewing a page (see the getView method) and makes 
 * various utilities available to sub-classes.
 * 
 * @author wtaylor
 *
 */
abstract class viewConverter_ViewConverter
{
	protected $HTML;
	
	function __construct() {
		$this->HTML = new HTML();	
	}
	
	/**
	 * Set the given key/values as dynamic properties. 
	 * @param array $properties the property names/values to set
	 */
	protected function setProperties($properties) {
		foreach($properties as $name => $value) {
			$this->$name = $value;
		}
	}
		
	protected function escapeHtml($text) {
		return HTML::escapeHtml($text);		
	}
	
	/**
	 * Utility method for prefixing the given URL with the application's
	 * context. This method also acts as an alias for use in heredocs.
	 * @param string $url the URL 
	 * @return string
	 */
	protected function contextUrl($url) {
		return Reggie::contextUrl($url);
	}
	
	/**
	 * Utility method for reading the contents of the given file. 
	 * @param string $name the file name
	 * @return string
	 */
	protected function getFileContents($name) {
		$file = str_replace('_', '/', $name).'.php';
		
		ob_start();
		require $file;
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	/**
	 * Alias needed for use in heredocs. Returns the HTML/JS for a table-based
	 * form layout. Form submission is done via Ajax.
	 *  
	 * @param string $url the form's action attribute value
	 * @param string $action the server-side action to perform
	 * @param string $rows HTML fragment
	 * @param string $buttonText the text to display in the submit button
	 * @return string
	 */
	protected function xhrTableForm($url, $action, $rows, $buttonText = 'Save', 
									$errorText = 'There was a problem saving. Please try again.', $useAjax = true) {
		$form = new fragment_XhrTableForm($url, $action, $rows, $buttonText, $errorText, $useAjax);
		return $form->html();
	}
	
	/**
	 * Alias needed for use in heredocs. Returns the HTML for a table-based form
	 * whose visibility is triggered by a click on a link. Form submission is 
	 * done via Ajax.
	 *
	 * @param string $link the text used in the trigger to display the form
	 * @param string $url the form's action attribute value
	 * @param string $action the server-side action to perform
	 * @param string $rows HTML fragment
	 * @return string
	 */
	protected function xhrAddForm($link, $url, $action, $rows) {
		$form = new fragment_XhrAddForm($link, $url, $action, $rows);
		return $form->html();
	}
	
	/**
	 * Alias needed for use in heredocs. Returns the HTML for a table-based
	 * form layout. Form submission is standard--no Ajax.
	 * 
	 * @param string $url the form's action attribute value
	 * @param string $action the server-side action to perform
	 * @param string $rows HTML fragment
	 * @param string $buttonText the text to display in the submit button
	 * @return string
	 */
	protected function tableForm($url, $action, $rows, $buttonText) {
		$form = new fragment_TableForm($url, $action, $rows, $buttonText);
		return $form->html();
	}

	/**
	 * Alias needed for use in heredocs. Returns the HTML for a pair of up/down 
	 * arrows.
	 *
	 * @param array $config the arrow configuration
	 */
	protected function arrows($config) {
		$arrows = new fragment_Arrows($config);
		return $arrows->html();
	}
	
	/**
	 * Additional content for the HTML document's head.
	 * 
	 * @return string
	 */
	protected abstract function head();
	
	/**
	 * Additional content for the HTML document's body.
	 *
	 * @return string
	 */
	protected abstract function body();
	
	/**
	 * Returns the HTML for viewing a page. This includes all standard CSS and JavaScript needed
	 * in general. Additional includes can be made by implementing the head and/or body methods.
	 * 
	 * @param array $properties dynamic properties used to create the HTML
	 * @return template_Template
	 */
	public function getView($properties) {
		$this->setProperties($properties);

		header('Content-Type: text/html; charset=utf-8');
		
		// set the title if given, otherwise it can be set in the head() method.
		$title = empty($this->title)? '' : "<title>{$this->title}</title>";
		
		$html = <<<_
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
			<html>
				<head>
					{$title}
					
					{$this->HTML->css(array('href' => '/js/dojo/resources/dojo.css'))}
					{$this->HTML->css(array('href' => '/js/dijit/themes/dijit.css'))}
					{$this->HTML->css(array('href' => '/js/dijit/themes/claro/claro.css'))}
					{$this->HTML->css(array('href' => '/js/dojox/form/resources/BusyButton.css'))}
							
					{$this->HTML->script(array('src' => '/js/dojo/dojo.js'))}
					
					<style type="text/css">
						#script-enabled-content {
							visibility: hidden;
						}
						
						noscript p {
							color: black;
							font-size: 2em;
							font-weight: bold;
							text-align: center;
						}
					</style>
				
					{$this->head()}
					
					<script type="text/javascript">
						dojo.addOnLoad(function() {
							setTimeout(function() {
								dojo.query(dojo.byId("page-render-time")).orphan();
							}, 3000);
						
							var messages = dojo.byId("xhr-response");
							if(messages) {
								dojo.require("hhreg.validation");
								hhreg.validation.showMessages(dojo.fromJson(messages.value), messages.form);
							}
						});
						
						document.write('<style type="text/css"> #script-enabled-content { visibility: visible; } </style>');
					</script>
				</head>
				
				<body class="claro">
					<noscript>
						<p>
							This site requires JavaScript. Please enable JavaScript in your browser.
						</p>
					</noscript>
					
					<div id="script-enabled-content">
						{$this->HTML->hidden(array(
							'id' => 'reggie.contextPath',
							'name' => 'reggie.contextPath',
							'value' => $this->contextUrl('/')
						))}
					
						{$this->body()}
						
						<div class="divider"></div>
				
						<div id="page-render-time" style="position:fixed;bottom:0;background-color:#333;color:#aaa;">Page Rendered in {$this->pageRenderTime()}s</div>
					</div>
				</body>
			</html>
_;

		return new template_TemplateWrapper($html);
	}
	
	private function pageRenderTime() {
		return time() - SessionUtil::getRequestStartTime();
	}
}

?>