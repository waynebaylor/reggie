<?php

class fragment_regOptionPrice_Add extends template_Template
{
	private $option;
	private $event;
	
	function __construct($event, $option) {
		parent::__construct();
		
		$this->option = $option;
		$this->event = $event;
	}
	
	public function html() {
		// this is somewhat fragile. the idea is to find a way to distinguish RegOption and 
		// VariableQuantityOption. RegOption does not have a 'sectionId' field, so we can use
		// that form now.
		$action = isset($this->option['sectionId'])? 'addVariableQuantityPrice' : 'addRegOptionPrice';
		
		$form = new fragment_XhrAddForm(
			'Add Option Price',
			'/admin/regOption/RegOptionPrice',
			$action,
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Description</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'regOptionId',
						'value' => $this->option['id']
					))}
					{$this->HTML->text(array(
						'name' => 'description',
						'value' => ''
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Start Date/Time</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'startDate',
						'value' => '',
						'size' => '16',
						'maxlength' => '16'
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">End Date/Time</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'endDate',
						'value' => '',
						'size' => '16',
						'maxlength' => '16'
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Price</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'price',
						'value' => '',
						'size' => '7'
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Visible To</td>
				<td>
					<select name="regTypeIds[]" multiple="multiple" size="5">
						{$this->getVisibleTo()}
					</select>
				</td>
			</tr>
_;
	}
	
	private function getVisibleTo() {
		$html = <<<_
			<option value="-1" selected="selected">All</option>	
_;
		
		foreach($this->event['regTypes'] as $regType) {
			$html .= <<<_
				<option value="{$regType['id']}">({$regType['code']}) {$regType['description']}</option>		
_;
		}
		
		return $html;
	}
}

?>