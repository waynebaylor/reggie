
{
	"identifier": "id",
	"items": [
		<?php foreach($this->emailTemplates as $index => $template): ?>
			<?php echo json_encode(array(
				'id' => $template->id,
				'status' => $template->enabled,
				'contactField' => $template->fieldName,
				'fromAddress' => $template->fromAddress,
				'bccAddress' => $template->bcc,
				'registrationTypes' => $template->availableTo
			)) ?>
			<?php echo ($index < count($this->emailTemplates)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}
		
				
			

