<?php

class fragment_regType_Edit extends template_Template
{
	private $regType;

	function __construct($regType) {
		parent::__construct();
		
		$this->regType = $regType;
	}

	public function html() {
		$form = new fragment_XhrTableForm('/admin/regType/RegType', 'saveRegType', $this->getFormRows());

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
			$html .= <<<_
				<div>
					{$this->HTML->checkbox(array(
						'label' => $category['displayName'],
						'id' => "categoryId_{$category['id']}",
						'name' => 'categoryIds[]',
						'value' => $category['id'],
						'checked' =>  model_RegType::isVisibleTo($this->regType, $category)
					))}
				</div>
_;
		}
			
		return $html;
	}
}

?>