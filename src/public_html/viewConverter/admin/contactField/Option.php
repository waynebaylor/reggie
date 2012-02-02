<?php

class viewConverter_admin_contactField_Option extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Field Option';
	}
	
	protected function body() {
		$body = parent::body();

		$form = new fragment_XhrTableForm(array(
			'url' => '/admin/contactField/Option',
			'action' => 'saveOption',
			'rows' => $this->getFormRows()
		));
		
		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			{$breadcrumbs->html()}
			
			<div id="content">
				<div class="fragment-edit">
					<h3>Edit Option</h3>
					
					{$form->html()}
				</div>
			</div>
_;
		
		return $body;
	}
	
	public function getAddOption($properties) {
		$this->setProperties($properties);
		return new fragment_contactFieldOption_List($this->event, $this->field);
	}
	
	public function getRemoveOption($properties) {
		$this->setProperties($properties);
		return new fragment_contactFieldOption_List($this->event, $this->field);
	}
	
	public function getSaveOption($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getMoveOptionUp($properties) {
		$this->setProperties($properties);
		return new fragment_contactFieldOption_List($this->event, $this->field);
	}
	
	public function getMoveOptionDown($properties) {
		$this->setProperties($properties);
		return new fragment_contactFieldOption_List($this->event, $this->field);
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="label required">Label</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->eventId
					))}
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->option['id']
					))}
					{$this->HTML->text(array(
						'name' => 'displayName',
						'value' => $this->escapeHtml($this->option['displayName'])
					))}
				</td>
			</tr>
			<tr>
				<td class="label">Restrictions</td>
				<td>
					{$this->HTML->checkbox(array(
						'label' => 'Selected By Default',
						'name' => 'defaultSelected',
						'value' => 'T',
						'checked' => ($this->option['defaultSelected'] === 'T')
					))}
				</td>
			</tr>
_;
	}
}

?>