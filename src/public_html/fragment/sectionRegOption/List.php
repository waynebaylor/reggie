<?php

class fragment_sectionRegOption_List extends template_Template
{
	private $group;
	private $event;
	
	function __construct($event, $group) {
		parent::__construct();
		
		$this->group = $group;
		$this->event = $event;
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Registration Options</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th></th>
						<th>Code</th>
						<th>Description</th>
						<th>Restrictions</th>
						<th>Options</th>
					</tr>
					{$this->getOptions()}
				</table>
			</div>	
_;
	}
	
	private function getOptions() {
		$html = '';
		
		$options = $this->group['options'];
		foreach($options as $option) {
			$arrows = new fragment_Arrows(array(
				'href' => '/admin/regOption/RegOption',
				'parameters' => array(
					'eventId' => $this->event['id']
				),
				'up' => array(
					'action' => 'moveOptionUp',
					'id' => $option['id']
				),
				'down' => array(
					'action' => 'moveOptionDown',
					'id' => $option['id']
				)
			));
			
			if(empty($option['text'])) {
				$cells = <<<_
					<td>
						{$this->escapeHtml($option['code'])}
					</td>
					<td>
						{$this->escapeHtml($option['description'])}
					</td>
					<td>
						{$this->getRestrictions($option)}
					</td>			
_;
			}
			else {
				$cells = <<<_
					<td colspan="3">
						{$this->escapeHtml($option['text'])}
					</td>
_;
			}			
			
			$html .= <<<_
				<tr>
					<td>
						{$arrows->html()}
					</td>
					{$cells}
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/admin/regOption/RegOption',
							'parameters' => array(
								'action' => 'view',
								'id' => $option['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/regOption/RegOption',
							'parameters' => array(
								'action' => 'removeOption',
								'id' => $option['id'],
								'eventId' => $this->event['id']
							),
							'class' => 'remove'
						))}
					</td>
_;
		}
		
		return $html;
	}
	
	private function getRestrictions($option) {
		$default = $option['defaultSelected'] === 'T'? 'Selected By Default' : '';
		$showPrice = $option['showPrice'] === 'T'? 'Show Price' : '';
		$capacity = (is_numeric($option['capacity']) && $option['capacity'] > 0)? "Capacity: {$this->escapeHtml($option['capacity'])}" : '';
		
		return <<<_
			<div>
				{$default}
			</div>
			<div>
				{$showPrice}
			</div>
			<div>
				{$capacity}
			</div>
_;
	}
}

?>