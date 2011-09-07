
{
	"identifier": "id",
	"items": [
		<?php foreach($this->templates as $index => $template): ?>
		{
			"id": <?php echo $template['id'] ?>,
			"eventId": <?php echo $template['eventId'] ?>,
			"name": "<?php echo $this->escapeHtml($template['name']) ?>",
			"type": "<?php $view_templateType = model_BadgeTemplateType::valueOf($template['type']); echo $this->escapeHtml($view_templateType['name']) ?>",
			"appliesToAll": <?php echo $template['appliesToAll']? 'true' : 'false' ?>,
			"appliesTo": [
				<?php foreach($template['appliesTo'] as $regTypeIndex => $regType): ?>
				{
					"id": <?php echo $regType['id'] ?>,
					"code": "<?php echo $regType['code'] ?>",
					"description": "<?php echo $this->escapeHtml($regType['description']) ?>"
				}
				<?php echo ($regTypeIndex < count($template['appliesTo'])-1)? ',' : '' ?>
				<?php endforeach; ?>
			]
		}
		<?php echo ($index < count($this->templates)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

