<?php

class fragment_editRegistrations_regOption_Add extends template_Template
{
	private $event;
	private $registration;
	
	function __construct($event, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->registration = $registration;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Registration Option', 
			'/admin/registration/RegOption', 
			'addRegOption', 
			$this->getFormRows()
		);
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td>Description</td>
				<td>
					{$this->HTML->select(array(
						'name' => 'regOptionId',
						'value' => '',
						'items' => $this->getOptions()
					))}
				</td>
			</tr>
_;
	}

	private function getOptions() {
		$opts = array();
		
		$groups = model_Event::getRegOptionGroups($this->event);
		foreach($groups as $group) {
			$opts = array_merge($opts, $this->getGroupOptions($group));
		}
		
		return $opts;
	}
	
	private function getGroupOptions($group) {
		$opts = array();
		
		foreach($group['options'] as $option) {
			$opts[] = array(
				'label' => $option['description'],
				'value' => $option['id']
			);
			
			foreach($option['groups'] as $subGroup) {
				$opts = array_merge($opts, $this->getGroupOptions($subGroup));
			}
		}

		return $opts;
	}
}
	
?>