<?php

class fragment_variableQuantityOption_List extends template_Template
{
	private $event;
	private $section;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->event = $event;
		$this->section = $section;
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Variable Quantity Options</h3>

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
		
		$options = $this->section['content'];
		foreach($options as $option) {
			$arrows = new fragment_Arrows(array(
				'href' => '/admin/regOption/VariableQuantity',
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

			$capacity = !empty($option['capacity'])? 'Capacity: '.$this->escapeHtml($option['capacity']) : '';
			
			$html .= <<<_
				<tr>
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
						{$capacity}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/admin/regOption/VariableQuantity',
							'parameters' => array(
								'action' => 'view',
								'id' => $option['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/regOption/VariableQuantity',
							'parameters' => array(
								'action' => 'removeOption',
								'id' => $option['id'],
								'eventId' => $this->event['id']
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		
		return $html;
	}
}

?>