<?php

require_once 'HTML.php';
require_once 'template/Template.php';

class fragment_Arrows extends template_Template
{
	private $config;
	
	function __construct($config) {
		parent::__construct();
		
		$this->config = $config;	
	}
	
	public function html() {
		return <<<_
			<div class="order-arrows">
				{$this->HTML->link(array(
					'label' => '<img class="up-arrow" src="/images/up_shadow.gif" title="Move Up" alt="Move Up"/>',
					'href' => $this->config['href'],
					'parameters' => array_merge($this->config['up'], $this->config['parameters'])
				))}
				<br/>
				{$this->HTML->link(array(
					'label' => '<img class="down-arrow" src="/images/down_shadow.gif" title="Move Down" alt="Move Down"/>',
					'href' => $this->config['href'],
					'parameters' => array_merge($this->config['down'], $this->config['parameters'])
				))}
			</div>
_;
	}
}

?>