<?php

class fragment_contactField_Edit extends template_Template
{
	private $field;
	private $regTypes;
	
	function __construct($field, $regTypes) {
		parent::__construct();
		
		$this->field = $field;	
		$this->regTypes = $regTypes;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/contactField/ContactField', 
			'save', 
			$this->getFormRows());
		
		return <<<_
			<div class="fragment-edit">			
				<h3>Edit Information Field</h3>

				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Label</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->field['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->field['eventId']
					))}
					{$this->HTML->textarea(array(
						'class' => 'expanding',
						'name' => 'displayName',
						'value' => $this->escapeHtml($this->field['displayName']),
						'rows' => 5,
						'cols' => 75
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Code</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'code',
						'value' => $this->field['code'],
						'size' => '10'
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Type</td>
				<td>
					{$this->HTML->select(array(
						'name' => 'formInputId',
						'value' => $this->field['formInput']['id'],
						'items' => $this->getInputTypes()
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Restrictions</td>
				<td>
					{$this->getRestrictions()}
				</td>
			</tr>
			<tr>
				<td class="label">Visible To</td>
				<td>
					{$this->HTML->select(array(
						'name' => 'regTypeIds[]',
						'value' => $this->field['visibleToAll']? '-1' : $this->getVisibleToValues(),
						'multiple' => 'multiple',
						'size' => '5',
						'items' => $this->getVisibleTo()
					))}
				</td>
			</tr>
_;
	}
	
	private function getInputTypes() {
		$opts = array();
		
		$types = model_FormInput::values();
		foreach($types as $type) {
			$opts[] = array(
				'label' => $type['displayName'],
				'value' => $type['id']
			);
		}
		
		return $opts;
	}
	
	private function getRestrictions() {
		$html = '';
		
		$validationRules = model_Validation::values();
		foreach($validationRules as $index => $rule) {
			$ruleId = intval($rule['id'], 10);
			
			$cssClasses = implode(' ', $this->getAppliesToFormInputs($rule));

			$html .= <<<_
				<div class="restriction {$cssClasses}">
_;
			if($ruleId === model_Validation::$REQUIRED) {
				$html .= $this->HTML->checkbox(array(
							'name'    => "validationRules_{$rule['id']}",
							'value'   => 'T',
							'checked' => $this->getValidationValue($rule),
							'label'   => $rule['displayName']
						));
			}
			else {
				$html .= <<<_
					{$this->HTML->text(array(
						'name'  => "validationRules_{$rule['id']}",
						'value' => $this->getValidationValue($rule),
						'size'  => '5'
					))}
					{$rule['displayName']}			
_;
			}
			
			$html .= '</div>';
		}
		
		$attributes = model_Attribute::values();
		foreach($attributes as $index => $attribute) {
			$cssClasses = implode(' ', $this->getAppliesToFormInputs($attribute));
			
			$html .= <<<_
				<div class="restriction {$cssClasses}">
					{$this->HTML->text(array(
						'name'  => "attributes_{$attribute['id']}",
						'value' => $this->getAttributeValue($attribute),
						'size'  => '5'
					))}
					{$attribute['displayName']}
				</div>
_;
		}
		
		return $html;		
	}
	
	/**
	 * used for CSS class names.
	 * 
	 * returns the names of the form inputs that can have
	 * the given attribute/validation rule.
	 */
	private function getAppliesToFormInputs($obj) {
		$inputNames = array();
		
		$inputs = model_FormInput::values();
		foreach($inputs as $input) {
			$attrs = $input['attributes'];
			foreach($attrs as $attr) {
				if($attr['name'] === $obj['name']) {
					$inputNames[] = 'formInput_'.$input['id'];
				}
			}	
			
			$rules = $input['validationRules'];
			foreach($rules as $rule) {
				if($rule['name'] === $obj['name']) {
					$inputNames[] = 'formInput_'.$input['id'];
				}
			}
		}

		return $inputNames;
	}
	
	private function getValidationValue($rule) {
		$fieldRules = $this->field['validationRules'];
		foreach($fieldRules as $fieldRule) {
			if(intval($fieldRule['id'], 10) === intval($rule['id'])) {
				return $fieldRule['value'];
			}
		}
		
		return '';
	}
	
	private function getAttributeValue($attr) {
		$fieldAttrs = $this->field['attributes'];
		foreach($fieldAttrs as $fieldAttr) {
			if(intval($fieldAttr['id'], 10) === intval($attr['id'], 10)) {
				return $fieldAttr['value'];
			}	
		}
		
		return '';
	}
	
	private function getVisibleTo() {
		$opts = array();

		$opts[] = array(
			'label' => 'All',
			'value' => '-1'
		);
		
		foreach($this->regTypes as $regType) {
			$opts[] = array(
				'label' => "({$regType['code']}) {$regType['description']}",
				'value' => $regType['id']
			);
		}

		return $opts;
	}
	
	private function getVisibleToValues() {
		$values = array();
		
		foreach($this->regTypes as $regType) {
			if(model_ContactField::isVisibleTo($this->field, $regType)) {
				$values[] = $regType['id'];
			}
		}
		
		return $values;
	}
}
?>