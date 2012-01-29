<?php

class fragment_reg_regOption_RegOptions extends template_Template
{
	private $group;
	private $regTypeId;
	private $selectedOptions;
	
	function __construct($group, $regTypeId, $selectedOpts, $pageId) {
		parent::__construct();
		
		$this->group = $group;
		$this->regTypeId = $regTypeId;
		$this->selectedOptions = $selectedOpts;
		$this->pageId = $pageId;
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
			if(empty($option['text'])) {
				$price = $this->getPrice($option);
				
				if(!empty($price)) {
					$groupsTemplate = new fragment_reg_regOptionGroup_RegOptionGroups($option['groups'], $this->regTypeId, $this->selectedOptions, $this->pageId);
					$optionGroupsHtml = $groupsTemplate->html();
					
					$html .= <<<_
						<tr>
							<td class="reg-option">
								{$this->getOption($option)}
								{$optionGroupsHtml}
							</td>
							<td class ="price">
								{$price}
							</td>
						</tr>
_;
				}				
			}
			else {
				$html .= $option['text'];
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
		
		if($this->optionAtCapacity($option)) {
			$config['disabled'] = 'disabled';
		}
		
		if('T' === $this->group['multiple']) {
			// add the brackets so we can handle multiple checks if necessary.
			$config['name'] .= '[]';
			return $this->HTML->checkbox($config);
		}
		else {
			return $this->HTML->radio($config);
		}			
	}
	
	private function getPrice($option) {
		$price = model_RegOption::getPrice(array('id' => $this->regTypeId), $option);

		if(!empty($price)) {
			// check option capacity first.
			if($this->optionAtCapacity($option)) {
				return 'Sold out.';
			}
			else {
				if($option['showPrice'] !== 'T') {
					// use a space if the price won't be displayed.
					return '&nbsp;';
				}
				else {
					return '$'.number_format($price['price'], 2);
				}
			}
		}
		
		return null;
	}
	
	/**
	 * check if the given option should be checked.
	 * 
	 * @param $option
	 */
	private function isOptionChecked($name, $option) {
		if(array_key_exists($name, $this->selectedOptions)) {
			$value = $this->selectedOptions[$name];
			
			// checkboxes could have multiple checked, hence the value array.
			if(is_array($value)) {
				return in_array($option['id'], $value);
			}
			// radio can only have one value.
			else {
				return $option['id'] === $value;
			}
		}
		else if(!in_array($this->pageId, model_reg_Session::getCompletedPages())) {
			return $option['defaultSelected'] === 'T';
		}
		
		return false;
	}
	
	private function optionAtCapacity($option) { 
		if(is_numeric($option['capacity']) && $option['capacity'] > 0) {
			$currentCount = db_reg_RegistrationManager::getInstance()->findOptionCount($option); 
			return $currentCount >= $option['capacity'];
		}
		
		return false;
	}
}

?>