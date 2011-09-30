
{
	"identifier": "id",
	"items": [
		<?php foreach($this->emailTemplates as $index => $template): ?>
		{
			"id": <?php echo $template->id ?>,
			"status": "<?php echo $template->enabled ?>",
			"contactField": "<?php echo $template->fieldName ?>",
			"fromAddress": "<?php echo $template->fromAddress ?>",
			"bccAddress": "<?php echo $template->bcc ?>",
			"registrationTypes": "<?php echo $template->availableTo ?>"
		}
		<?php echo ($index < count($this->emailTemplates)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}
		
				
			

