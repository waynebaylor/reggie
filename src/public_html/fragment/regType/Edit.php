<?php

class fragment_regType_Edit extends template_Template
{
	private $regType;

	function __construct($regType) {
		parent::__construct();
		
		$this->regType = $regType;
	}

	public function html() {
		$form = new fragment_XhrTableForm('/action/admin/regType/RegType', 'saveRegType', $this->getFormRows());

		return <<<_
			<div class="fragment-edit">
				<h3>Edit Registration Type</h3>
				
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
						'value' => $this->regType['id']
					))}
					{$this->HTML->text(array(
						'name' => 'description',
						'value' => $this->regType['description']
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Code</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'code',
						'value' => $this->regType['code']
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Visible To</td>
				<td>
					{$this->getVisibleTo()}
				</td>
			</tr>	
_;
	}

	private function getVisibleTo() {
		$html = '';

		$categories = model_Category::values();
		foreach($categories as $category) {
			$checked = model_RegType::isVisibleTo($this->regType, $category)? 'checked="checked"' : '';
			$html .= <<<_
				<div>
					<input type="checkbox" id="categoryId_{$category['id']}" name="categoryIds[]" value="{$category['id']}" {$checked}/>
					<label for="categoryId_{$category['id']}">{$category['displayName']}</label>
				</div>
_;
		}
			
		return $html;
	}
}

?>