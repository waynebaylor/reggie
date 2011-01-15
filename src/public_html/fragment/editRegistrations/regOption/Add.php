<?php

class fragment_editRegistrations_regOption_Add extends template_Template
{
	private $event;
	private $report;
	private $registration;
	
	function __construct($event, $report, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->report = $report;
		$this->registration = $registration;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Registration Option', 
			'/admin/registration/RegOption', 
			'addRegOptions', 
			$this->getFormRows()
		);
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		$html = '';

		foreach($this->getOptions() as $opt) {
			$html .= <<<_
			<tr>
				<td>
					{$this->HTML->checkbox(array(
						'name' => 'regOpts[]',
						'value' => $opt['value']
					))}
				</td>
				<td>
					<label for="regOpts[]_{$opt['value']}">{$opt['label']}</label>
				</td>
				<td>
					{$this->getOptionPrices($opt['value'], $opt['prices'])}
				</td>
			</tr>
_;
		}
		
		return <<<_
			<tr>
				<td></td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'registrationId',
						'value' => $this->registration['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'reportId',
						'value' => $this->report['id']
					))}
					
					<table class="admin" style="border:none;">
						{$html}
					</table>
				</td>
			</tr>
_;
	}

	private function getOptionPrices($optId, $prices) {
		$optPrices = array();

		foreach($prices as $p) {
			$optPrices[] = array(
				'label' => '$'.number_format($p['price'], 2)."({$p['description']})",
				'value' => $p['id'] 
			);	
		}
		
		return $this->HTML->select(array(
			'name' => "regOptPrice_{$optId}",
			'value' => '',
			'items' => $optPrices
		));	
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
				'value' => $option['id'],
				'prices' => db_RegOptionPriceManager::getInstance()->findByRegOption($option)
			);
			
			foreach($option['groups'] as $subGroup) {
				$opts = array_merge($opts, $this->getGroupOptions($subGroup));
			}
		}

		return $opts;
	}
}
	
?>