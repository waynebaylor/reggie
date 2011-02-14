<?php

abstract class viewConverter_ViewConverter
{
	protected $HTML;
	
	/**
	 * required properties: title.
	 */
	function __construct() {
		$this->HTML = new HTML();	
	}
	
	protected function setProperties($properties) {
		foreach($properties as $name => $value) {
			$this->$name = $value;
		}
	}
		
	protected function escapeHtml($text) {
		return htmlspecialchars($text);			
	}
	
	protected function contextUrl($url) {
		return Reggie::contextUrl($url);
	}
	
	protected function getFileContents($name) {
		$file = str_replace('_', '/', $name).'.php';
		
		ob_start();
		require $file;
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	protected function xhrTableForm($url, $action, $rows) {
		$form = new fragment_XhrTableForm($url, $action, $rows);
		return $form->html();
	}
	
	protected function xhrAddForm($link, $url, $action, $rows) {
		$form = new fragment_XhrAddForm($link, $url, $action, $rows);
		return $form->html();
	}
	
	protected function arrows($config) {
		$arrows = new fragment_Arrows($config);
		return $arrows->html();
	}
	
	protected abstract function head();
	
	protected abstract function body();
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$html = <<<_
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
			<html>
				<head>
					<title>{$this->title}</title>
					
					{$this->HTML->css(array('href' => '/js/dojo/resources/dojo.css'))}
					{$this->HTML->css(array('href' => '/js/dijit/themes/dijit.css'))}
					{$this->HTML->css(array('href' => '/js/dijit/themes/claro/claro.css'))}
							
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
					</div>
				</body>
			</html>
_;

		return new template_TemplateWrapper($html);
	}
}

?>