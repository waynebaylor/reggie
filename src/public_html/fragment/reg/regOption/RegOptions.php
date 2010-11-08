<?php

class fragment_reg_regOption_RegOptions extends template_Template
{
	private $group;
	
	function __construct($group) {
		parent::__construct();
		
		$this->group = $group;
	}	
	
	public function html() {
		return <<<_
			<table class="reg-options">
				{$this->getGroupRows()}
			</table>
_;
	}
	
	private function getGroupRows() {
		$html = '';

		foreach($this->group['options'] as $option) {
			$price = $this->getPrice($option);
			
			if(!empty($price)) {
				$groupsTemplate = new fragment_reg_regOptionGroup_RegOptionGroups($option['groups']);
				$optionGroupsHtml = $groupsTemplate->html();
				
				$priceDisplay = '$'.number_format($price['price'], 2);
				
				$html .= <<<_
					<tr>
						<td class="reg-option">
							{$this->getOption($option)}
							{$optionGroupsHtml}
						</td>
						<td class ="price">
							{$priceDisplay}
						</td>
					</tr>
_;
			}
		}	

		return $html;
	}
	
	private function getOption($option) {
		$name = model_ContentType::$REG_OPTION.'_'.$option['parentGroupId'];
		
		$config = array(
			'label' => $option['description'],
			'name' => $name,
			'value' => $option['id'],
			'checked' => $this->isOptionChecked($name, $option)
		);

		if('true' === $this->group['multiple']) {
			// add the brackets so we can handle multiple checks if necessary.
			$config['name'] .= '[]';
			return $this->HTML->checkbox($config);
		}
		else {
			return $this->HTML->radio($config);
		}
	}
	
	private function getPrice($option) {
		$regType = model_RegSession::getRegType();
		
		return model_RegOption::getPrice($regType, $option);
	}
	
	/**
	 * check if the given option should be checked.
	 * 
	 * @param $option
	 */
	private function isOptionChecked($name, $option) {
		$value = model_RegSession::getRegOption($name);
		
		// checkboxes could have multiple checked, hence the value array.
		if(is_array($value)) {
			return in_array($option['id'], $value);
		}
		// radio can only have one value.
		else {
			return $option['id'] === $value;
		}
	}
}

?>