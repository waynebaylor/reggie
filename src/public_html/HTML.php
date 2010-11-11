<?php

class HTML
{
	private static function getAttributeString($config) {
		$attrs = '';
		foreach($config as $attr => $value) {
			if(!is_array($value)) {
				$attrs .= $attr.'="'.$value.'" ';
			}
		}

		return $attrs;
	}
	
	/**
	 * Creates a hidden input.
	 * @param array $config 
	 */
	public function hidden($config) {
		$attrs = self::getAttributeString($config);
		return <<<_
			<input type="hidden" {$attrs} />	
_;
	}
	
	/**
	 * Creates a text input.
	 * @param $config attr="value" pairs
	 */
	public function text($config) {
		$attrs = self::getAttributeString($config);

		return <<<_
			<input type="text" {$attrs} />
_;
	}
	
	public function textarea($config) {
		$value = $config['value'];
		unset($config['value']);
		
		$attrs = self::getAttributeString($config);
		
		return <<<_
			<textarea {$attrs}>{$value}</textarea>	
_;
	}
	
	/**
	 * Creates a checkbox input.
	 * @param $config attr="value" pairs
	 */
	public function checkbox($config) {
		// convert the checked value to the valid value.		
		if(isset($config['checked'])) {
			if(isset($config['checked']) && in_array($config['checked'], array('checked', 'true', true))) {
				$config['checked'] = 'checked';
			}
			else {
				unset($config['checked']);
			}
		}
		
		// if the id is not given, then make it.
		if(empty($config['id'])) {
			$id = $config['name'].'_'.$config['value'];
		}
		else {
			$id = $config['id'];
			unset($config['id']);	
		}
		
		// if a label is given, then display it next to the checkbox.
		if(isset($config['label'])) {
			$label = <<<_
				<label for="{$id}">{$config['label']}</label>
_;
			unset($config['label']);
		}
		else {
			$label = '';
		}
		
		// add/set the right css class for the input.
		if(isset($config['class'])) {
			$config['class'] = $config['class'].' checkbox-input';	
		}
		else {
			$config['class'] = 'checkbox-input';
		}
		
		$attrs = self::getAttributeString($config);
		
		return <<<_
			<table class="checkbox-label"><tr>
				<td>	
					<input type="checkbox" id="{$id}" {$attrs} />
				</td>
				<td> {$label}</td>
			</tr></table>
_;
	}
	
	/**
	 * Creates a group of checkbox inputs.
	 * @param $config (name, value, items[(label, value)])
	 */
	public function checkboxes($config) {
		// the [] at the end are needed so PHP will convert multiple "checks" into
		// an array of values when the user submits.
		if(substr($config['name'], -2) !== '[]') {
			$config['name'] = $config['name'].'[]';	
		}
		
		return $this->radioCheckInputs($config, true);
	}
	
	public function link($config) {
		$label = $config['label'];
		
		$url = $config['href'];
		if(isset($config['parameters'])) {
			// check if there is already a query in the URL. if not, then add the '?'.
			// if there is, then make sure the URL ends with an '&'.
			if(strpos($url, '?') === false) {
				$url .= '?';
			}
			else {
				$url = rtrim($url, '&').'&';
			}
			
			foreach($config['parameters'] as $param => $value) {
				$url .= $param.'='.$value.'&';
			}
		}
		$url = trim($url, '&');
		
		unset($config['label']);
		unset($config['href']);
		unset($config['parameters']);
		
		return <<<_
			<a href="{$url}" {$this->getAttributeString($config)}>{$label}</a>	
_;
	}
	
	public function radio($config) {
		$label = $config['label'];
		unset($config['label']);
		
		if(isset($config['checked']) && in_array($config['checked'], array('checked', 'true', true))) {
			$config['checked'] = 'checked';
		}
		else {
			unset($config['checked']);
		}
		
		// if the id is given, then use it.
		if(empty($config['id'])) {
			$id = $config['name'].'_'.$config['value'];
		}
		else {
			$id = $config['id'];
			unset($config['id']);	
		}

		// add/set the right css class for the input.
		if(isset($config['class'])) {
			$config['class'] = $config['class'].' radio-input';	
		}
		else {
			$config['class'] = 'radio-input';
		}
		
		$attrs = self::getAttributeString($config);
		
		return <<<_
			<table class="radio-label"></tr>
				<td>
					<input type="radio" id="{$id}" {$attrs} />
				</td>
				<td>
					<label for="{$id}">{$label}</label>
				</td>
			</tr></table>
_;
	}
	
	public function radios($config) {
		return $this->radioCheckInputs($config, false);
	}
	
	public function select($config) {
		$attrs = self::getAttributeString($config);
		
		$html = <<<_
			<select {$attrs}>	
_;
		// if no value is specified, then make the first option selected. 
		// isset() is used instead of empty() because we 0,'', etc to be
		// valid values.
		if(!isset($config['value'])) {
			$config['value'] = $config['items'][0]['value']; 	
		}
		
		foreach($config['items'] as $index => $item) {
			$checked = is_array($config['value'])? 
				in_array($item['value'], $config['value']) : 
				$config['value'] === $item['value'];
			
			$checked = $checked? 'selected="selected"' : '';
			
			$html .= <<<_
				<option value="{$item['value']}" {$checked}>{$item['label']}</option>
_;
		}
		
		return $html.'</select>';
	}
	
	private function radioCheckInputs($config, $multipleAllowed) {
		$html = '';

		foreach($config['items'] as $item) { 
			$item['name'] = $config['name'];
			
			// the 'value' attribute is not required and the 
			// inputs may be set as checked or not within
			// the item array.
			if(isset($config['value'])) {
				// multiple inputs could be selected.
				if(is_array($config['value'])) {
					$item['checked'] = in_array($item['value'], $config['value']);
				}
				else {
					$item['checked'] = ($item['value'] === $config['value']);
				}
			}
			
			$input = $multipleAllowed? $this->checkbox($item) : $this->radio($item);
			$html .= <<<_
				<div>
					{$input}
				</div>
_;
		}
		
		return $html;
	}
}

?>