<?php

require_once 'template/Template.php';
require_once 'HTML.php';
require_once 'fragment/Arrows.php';


class fragment_contactFieldOption_List extends template_Template
{
	private $field;
	private $event;
	
	function __construct($event, $field) {
		parent::__construct();
		
		$this->field = $field;
		$this->event = $event;
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Options</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th></th>
						<th>Label</th>
						<th>Restrictions</th>
						<th></th>
					</tr>
					{$this->getOptions()}
				</table>
			</div>
_;
	}
	
	private function getOptions() {
		$html = '';
		$evenRow = true;
		
		$options = $this->field['options'];
		foreach($options as $option) {
			$arrows = new fragment_Arrows(array(
				'href' => '/action/admin/contactField/Option',
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
						{$this->escapeHtml($option['displayName'])}
					</td>
					<td>
						{$this->getRestrictions($option)}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href'  => '/action/admin/contactField/Option',
							'parameters' => array(
								'action' => 'removeOption',
								'id'     => $option['id'],
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
	
	private function getRestrictions($option) {
		if($option['defaultSelected'] === 'true') {
			return 'Selected By Default';
		}
		
		return '';
	}
}

?>