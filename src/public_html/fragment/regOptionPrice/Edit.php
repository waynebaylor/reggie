<?php

class fragment_regOptionPrice_Edit extends template_Template
{
	private $price;
	private $regTypes;
	
	function __construct($price, $regTypes) {
		parent::__construct();
		
		$this->price = $price;
		$this->regTypes = $regTypes;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/action/admin/regOption/RegOptionPrice',
			'savePrice',
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-edit">			
				<h3>Edit Option Price</h3>

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
						'name' => 'id',
						'value' => $this->escapeHtml($this->price['id'])
					))}
					{$this->HTML->text(array(
						'name' => 'description',
						'value' => $this->escapeHtml($this->price['description'])
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Start Date/Time</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'startDate',
						'value' => $this->escapeHtml($this->price['startDate']),
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
						'value' => $this->escapeHtml($this->price['endDate']),
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
						'value' => $this->escapeHtml($this->price['price']),
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
		$selected = $this->price['visibleToAll']? 'selected="selected"' : '';
		$html = <<<_
			<option value="-1" {$selected}>All</option>	
_;

		foreach($this->regTypes as $regType) {
			$selected = !$this->price['visibleToAll'] && 
						model_RegOptionPrice::isVisibleTo($this->price, $regType)?
				'selected="selected"' : '';
			$html .= <<<_
				<option value="{$regType['id']}" {$selected}>
					({$regType['code']}) {$regType['description']}
				</option>
_;
		}
		
		return $html;
	}
}

?>