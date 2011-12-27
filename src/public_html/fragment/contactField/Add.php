<?php

class fragment_contactField_Add extends template_Template
{
	private $section;
	private $event;

	function __construct($event, $section) {
		parent::__construct();
		
		$this->section = $section;
		$this->event = $event;
	}

	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Information Field', 
			'/admin/contactField/ContactField', 
			'addField', 
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
				<td class="required label">Label</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'sectionId',
						'value'=> $this->section['id']
					))}
					
					{$this->HTML->textarea(array(
						'name' => 'displayName',
						'value' => '',
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
						'value' => '',
						'size' => '10'
					))}
				</td>
			</tr>
			<tr>	
				<td class="required label">Type</td>
				<td>
					{$this->HTML->select(array(
						'name' => 'formInputId',
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
						'value' => '-1',
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
			$cssClasses = implode(' ', $this->getAppliesToFormInputs($rule));

			if(intval($rule['id'], 10) === model_Validation::$REQUIRED) {
				$html .= <<<_
					<div class="restriction {$cssClasses}">
						{$this->HTML->checkbox(array(
							'label' => $rule['displayName'], 
							'name' => "validationRules_{$rule['id']}",
							'value' => 'T'
						))}
					</div>
_;
			}
			else {
				$html .= <<<_
					<div class="restriction {$cssClasses}">
						{$this->HTML->text(array(
							'name' => "validationRules_{$rule['id']}",
							'value' => '',
							'size' => '5'
						))}
						{$rule['displayName']}
					</div>
_;
			}
		}
		
		$attributes = model_Attribute::values();
		foreach($attributes as $index => $attribute) {
			$cssClasses = implode(' ', $this->getAppliesToFormInputs($attribute));
			
			$html .= <<<_
				<div class="restriction {$cssClasses}">
					{$this->HTML->text(array(
						'name' => "attributes_{$attribute['id']}",
						'value' => '',
						'size' => '5'
					))}
					{$attribute['displayName']}
				</div>
_;
		}
		
		return $html;		
	}
	
	private function getVisibleTo() {
		$opts = array();
		
		$opts[] = array(
			'label' => 'All',
			'value' => '-1' 
		);
		
		foreach($this->event['regTypes'] as $regType) {
			$opts[] = array(
				'label' => "({$regType['code']}) {$regType['description']}",
				'value' => $regType['id']
			);
		}
		
		return $opts;
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
}

?>