<?php 

class fragment_reg_Menu extends template_Template
{
	private $event;
	private $pageId;
	
	function __construct($event, $pageId) {
		parent::__construct();	
		
		$this->event = $event;
		$this->pageId = $pageId;
	}
	
	public function html() {
		return <<<_
			<div class="menu">
				<div class="menu-title">Menu</div>
				{$this->links()}
			</div>
_;
	}
	
	private function links() {
		$category = model_RegSession::getCategory();
		$visiblePages = model_EventPage::getVisiblePages($this->event, $category);

		$displayAsLink = true;
		
		$html = '';
		foreach($visiblePages as $index => $page) {
			// start at 1 not 0.
			$index = $index + 1;
			$current = $page['id'] === $this->pageId? 'current' : '';
			
			$html .= <<<_
				<div class="menu-link {$current}">
					{$index}. {$this->getLink($page)}
				</div>
_;
		}
		
		// the event may not have any payment options set up.
		if(!empty($this->event['paymentTypes'])) {
			$index = $index + 1;
			$current = $this->pageId === 'payment'? 'current' : '';
			$html .= <<<_
				<div class="menu-link {$current}">
					{$index}. Payment Information
				</div>		
_;
		}
		
		$index = $index + 1;
		$current = $this->pageId === 'summary'? 'current' : '';
		$html .= <<<_
			<div class="menu-link {$current}">
				{$index}. Review &amp; Confirm
			</div>
_;
		
		return $html;
	}

	private function getLink($page) {
		$currentReg = model_RegSession::getCurrent();
		$completedPages = model_RegSession::getCompletedPages();
		
		if(in_array($page['id'], $completedPages)) {
			$category = model_RegSession::getCategory();
			$cat = model_Category::code($category);
			
			return $this->HTML->link(array(
				'label' => $page['title'],
				'href' => "/event/{$this->event['code']}/{$cat}/{$page['id']}"
			));
		}
		else {
			return $page['title'];
		}
	}
}
?>