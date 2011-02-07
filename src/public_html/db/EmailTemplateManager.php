<?php

class db_EmailTemplateManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_EmailTemplateManager();
		}
		
		return self::$instance;
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);

		$obj['availableToAll'] = $this->isAvailableToAll($obj);
		$obj['availableTo'] = db_RegTypeManager::getInstance()->findForEmailTemplate($obj);
		
		return $obj;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				eventId,
				contactFieldId,
				enabled,
				fromAddress,
				bcc,
				subject,
				header,
				footer
			FROM
				EmailTemplate
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find email template.');		
	}
	
	public function findByEventId($eventId) {
		$sql = '
			SELECT
				id,
				eventId,
				contactFieldId,
				enabled,
				fromAddress,
				bcc,
				subject,
				header,
				footer
			FROM
				EmailTemplate
			WHERE
				eventId = :eventId
		';
		
		$params = array(
			'eventId' => $eventId
		);
		
		return $this->query($sql, $params, 'Find email template by event.');
	}
	
	public function findByEvent($event) {
		return $this->findByEventId($event['id']);
	}
	
	public function findByRegTypeId($eventId, $regTypeId) {
		$sql = '
			SELECT
				EmailTemplate.id,
				EmailTemplate.eventId,
				EmailTemplate.contactFieldId,
				EmailTemplate.enabled,
				EmailTemplate.fromAddress,
				EmailTemplate.bcc,
				EmailTemplate.subject,
				EmailTemplate.header,
				EmailTemplate.footer
			FROM
				EmailTemplate
			INNER JOIN
				RegType_EmailTemplate
			ON
				EmailTemplate.id = RegType_EmailTemplate.emailTemplateId
			WHERE
				EmailTemplate.eventId = :eventId
			AND
				EmailTemplate.enabled = :enabled
			AND 
				RegType_EmailTemplate.regTypeId is NULL
		';	
		
		$params = array(
			'eventId' => $eventId,
			'enabled' => 'true'
		);
		
		$template = $this->queryUnique($sql, $params, 'Find email template available to all.');
		
		if(empty($template)) {
			$sql = '
				SELECT
					EmailTemplate.id,
					EmailTemplate.eventId,
					EmailTemplate.contactFieldId,
					EmailTemplate.enabled,
					EmailTemplate.fromAddress,
					EmailTemplate.bcc,
					EmailTemplate.subject,
					EmailTemplate.header,
					EmailTemplate.footer
				FROM
					EmailTemplate
				INNER JOIN
					RegType_EmailTemplate
				ON
					EmailTemplate.id = RegType_EmailTemplate.emailTemplateId
				WHERE
					EmailTemplate.eventId = :eventId
				AND
					EmailTemplate.enabled = :enabled
				AND 
					RegType_EmailTemplate.regTypeId = :regTypeId
			';
			
			$params = array(
				'eventId' => $eventId,
				'enabled' => 'true',
				'regTypeId' => $regTypeId
			);
			
			$template = $this->queryUnique($sql, $params, 'Find email template by reg type id.');
		}
		
		return $template;
	}
	
	public function createEmailTemplate($params, $regTypeIds) {
		$sql = '
			INSERT INTO
				EmailTemplate(
					eventId,
					contactFieldId,
					enabled,
					fromAddress,
					bcc,
					subject,
					header,
					footer
				)
			VALUES(
				:eventId,
				:contactFieldId,
				:enabled,
				:fromAddress,
				:bcc,
				:subject,
				:header,
				:footer
			)
		';
		
		$this->execute($sql, $params, 'Create email template.');
		
		$emailTemplateId = $this->lastInsertId();
		
		// create reg type associations.
		$this->createRegTypeAssociation($emailTemplateId, $regTypeIds);
	}
	
	public function save($params, $regTypeIds) {
		$sql = '
			UPDATE
				EmailTemplate
			SET
				enabled = :enabled,
				contactFieldId = :contactFieldId,
				fromAddress = :fromAddress,
				bcc = :bcc,
				subject = :subject,
				header = :header,
				footer = :footer
			WHERE
				id = :id
		';
		
		$this->execute($sql, $params, 'Save email template.');
		
		$this->removeRegTypeAssociations($params['id']);
		$this->createRegTypeAssociation($params['id'], $regTypeIds);
	}
	
	private function isAvailableToAll($template) {
		$sql = '
			SELECT
				emailTemplateId
			FROM 
				RegType_EmailTemplate
			WHERE
				emailTemplateId = :id
			AND
				regTypeId is NULL
		';
		
		$params = array(
			'id' => $template['id']
		);
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if email template is visible to all reg types.');
		
		return !empty($result);
	}
	
	private function removeRegTypeAssociations($emailTemplateId) {
		$sql = '
			DELETE FROM
				RegType_EmailTemplate
			WHERE
				emailTemplateId = :emailTemplateId
		';	
		
		$params = array(
			'emailTemplateId' => $emailTemplateId
		);
		
		$this->execute($sql, $params, 'Remove reg type associations for email template.');
	}
	
	private function createRegTypeAssociation($emailTemplateId, $regTypeIds) {
		if(in_array(-1, $regTypeIds)) {
			$sql = '
				INSERT INTO
					RegType_EmailTemplate(
						emailTemplateId
					)
				VALUES(
					:emailTemplateId
				)
			';
			
			$params = array(
				'emailTemplateId' => $emailTemplateId
			);
			
			$this->execute($sql, $params, 'Set email template available to all reg types.');
		}
		else {
			$sql = '
				INSERT INTO
					RegType_EmailTemplate(
						regTypeId,
						emailTemplateId
					)
				VALUES(
					:regTypeId,
					:emailTemplateId
				)
			';
			
			foreach($regTypeIds as $regTypeId) {
				$params = array(
					'regTypeId' => $regTypeId,
					'emailTemplateId' => $emailTemplateId
				);	
				
				$this->execute($sql, $params, 'Create reg type associations for email template.');
			}
		}
	}
	
	public function delete($id) {
		$this->removeRegTypeAssociations($id);
		
		$sql = '
			DELETE FROM
				EmailTemplate
			WHERE
				id = :id
		';
		
		$params = array('id' => $id);
		
		$this->execute($sql, $params, 'Delete email template.');
	}
}

?>