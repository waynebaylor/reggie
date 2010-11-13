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
						<th>Description</th>
						<th>Code</th>
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
		$evenRow = true;
		
		$options = $this->group['options'];
		foreach($options as $option) {
			$arrows = new fragment_Arrows(array(
				'href' => '/action/admin/regOption/RegOption',
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
			
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd'; 
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>
						{$arrows->html()}
					</td>
					<td>
						{$this->escapeHtml($option['description'])}
					</td>
					<td>
						{$this->escapeHtml($option['code'])}
					</td>
					<td>
						{$this->getRestrictions($option)}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/action/admin/regOption/RegOption',
							'parameters' => array(
								'action' => 'view',
								'id' => $option['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/action/admin/regOption/RegOption',
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
		return <<<_
			<div>
				Selected By Default: {$this->escapeHtml($option['defaultSelected'])}
			</div>
			<div>
				Show Price: {$this->escapeHtml($option['showPrice'])}
			</div>
			<div>
				Capacity: {$this->escapeHtml($option['capacity'])}
			</div>
_;
	}
}

?>