<?php

class fragment_regOptionPrice_List extends template_Template
{
	private $option;
	private $event;
	
	function __construct($event, $option) {
		parent::__construct();
		
		$this->option = $option;
		$this->event = $event;	
	}
	
	public function html() {
		return <<<_
			<h3>Option Prices</h3>

			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th>Description</th>
						<th>Start Date/Time</th>
						<th>End Date/Time</th>
						<th>Price</th>
						<th>Visible To</th>
						<th>Options</th>
					</tr>
					{$this->getPrices()}
				</table>
			</div>
_;
	}
	
	private function getPrices() {
		$html = '';
		$evenRow = true;
		
		$prices = $this->option['prices'];
		foreach($prices as $price) {
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd';
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>{$this->escapeHtml($price['description'])}</td>
					<td>{$price['startDate']}</td>
					<td>{$price['endDate']}</td>
					<td>\${$price['price']}</td>
					<td>
						{$this->getVisibleTo($price)}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/admin/regOption/RegOptionPrice',
							'parameters' => array(
								'action' => 'view',
								'id' => $price['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/regOption/RegOptionPrice',
							'parameters' => array(
								'action' => 'removePrice',
								'id' => $price['id'],
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
	
	private function getVisibleTo($price) {
		$html = '';
		
		if($price['visibleToAll']) {
			$html .= '<div>All</div>';
		}
		else {
			$regTypes = $price['visibleTo'];
			foreach($regTypes as $regType) {
				$html .= <<<_
					<div>
						({$regType['code']}) {$regType['description']}
					</div>
_;
			}			
		}
		
		return $html;
	}
}

?>