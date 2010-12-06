<?php

class fragment_Arrows extends template_Template
{
	private $config;
	
	function __construct($config) {
		parent::__construct();
		
		$this->config = $config;	
	}
	
	public function html() {
		$upImg = $this->HTML->img(array(
			'class' => 'up-arrow',
			'src' => '/images/up_shadow.gif',
			'title' => 'Move Up',
			'alt' => 'Move Up'
		));
		
		$downImg = $this->HTML->img(array(
			'class' => 'down-arrow',
			'src' => '/images/down_shadow.gif',
			'title' => 'Move Down',
			'alt' => 'Move Down'
		));
		
		return <<<_
			<div class="order-arrows">
				{$this->HTML->link(array(
					'label' => $upImg,
					'href' => $this->config['href'],
					'parameters' => array_merge($this->config['up'], $this->config['parameters'])
				))}
				<br/>
				{$this->HTML->link(array(
					'label' => $downImg,
					'href' => $this->config['href'],
					'parameters' => array_merge($this->config['down'], $this->config['parameters'])
				))}
			</div>
_;
	}
}

?>