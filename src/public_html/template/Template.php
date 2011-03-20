<?php

abstract class template_Template
{
	protected $HTML;
	
	function __construct() {
		$this->HTML = new HTML();	
	}
	
	public abstract function html();
	
	protected function escapeHtml($text) {
		return htmlentities($text, ENT_QUOTES, 'UTF-8');
	}
	
	protected function contextUrl($url) {
		return Reggie::contextUrl($url);
	}
}
