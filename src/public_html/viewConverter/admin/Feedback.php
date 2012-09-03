<?php

class viewConverter_admin_Feedback extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Feedback';
	}
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$formHtml = $this->xhrTableForm(array(
			'url' => '/admin/Feedback',
			'action' => 'submitFeedback',
			'rows' => $this->getFormRows(),
			'buttonText' => 'Submit'
		));
		
		$html = <<<_
			{$formHtml}
			
			<script type="text/javascript">
				dojo.require("hhreg.xhrTableForm");
				
				var feedbackFormNode = dojo.query("form[name=submitFeedback]")[0];
				var textareaNode = dojo.query("textarea", feedbackFormNode)[0];
				
				var feedbackTextarea = new dijit.form.Textarea(
					{
						name: textareaNode.name,
						style: "width: 500px; min-height: 100px;"
					}, 
					textareaNode
				).startup();
				
				hhreg.xhrTableForm.bind(feedbackFormNode, function() {
					// hide the dialog after submitting form, but not too fast.
					setTimeout(function() {
						feedbackTextarea.set('value', '');
						dijit.byId("feedback-dialog").hide();	
					}, 500);
				});
			</script>
_;

		return new template_TemplateWrapper($html);
	}
	
	public function getSubmitFeedback($properties) {
		return new fragment_Success();
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td colspan="2">
					<textarea name="feedback"></textarea>
				</td>
			</tr>
_;
	}
}

?>