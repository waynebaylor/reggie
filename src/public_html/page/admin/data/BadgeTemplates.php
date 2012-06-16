
{
	"identifier": "id",
	"items": [
		<?php foreach($this->templates as $index => $template): ?>
			<?php 
				$view_templateType = model_BadgeTemplateType::valueOf($template['type']);
				$appliesTo = array();
				foreach($template['appliesTo'] as $regTypeIndex => $regType) {
					$appliesTo[] = array(
						'id' => $regType['id'],
						'code' => $regType['code'],
						'description' => $this->escapeHtml($regType['description'])
					);
				}
			?>
			<?php echo json_encode(array(
				'id' => $template['id'],
				'eventId' => $template['eventId'],
				'name' => $this->escapeHtml($template['name']),	
				'type' => $this->escapeHtml($view_templateType['name']),
				'editLink' => "/admin/badge/EditBadgeTemplate?eventId={$template['eventId']}&id={$template['id']}",
				'copyLink' => "/admin/badge/BadgeTemplates?a=copyTemplate&eventId={$template['eventId']}&id={$template['id']}",
				'appliesToAll' => $template['appliesToAll']? 'true' : 'false',
				'appliesTo' => $appliesTo
			)) ?>
			<?php echo ($index < count($this->templates)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}

