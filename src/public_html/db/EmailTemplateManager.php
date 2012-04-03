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
		$obj['availableTo'] = db_RegTypeManager::getInstance()->findForEmailTemplate(array(
			'eventId' => $obj['eventId'],
			'emailTemplateId' => $obj['id'],
			'availableToAll' => $obj['availableToAll']
		));
		
		return $obj;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
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
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find email template.');		
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEventId($params) {
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
		
		$params = ArrayUtil::keyIntersect($params, array('eventId'));
		
		return $this->query($sql, $params, 'Find email template by event.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function findByEvent($params) {
		return $this->findByEventId($params);
	}
	
	/**
	 * 
	 * @param array $params [eventId, regTypeId]
	 */
	public function findByRegTypeId($params) {
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
		
		$p = array(
			'eventId' => $params['eventId'],
			'enabled' => 'T'
		);
		
		$template = $this->queryUnique($sql, $p, 'Find email template available to all.');
		
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
			
			$p = array(
				'eventId' => $params['eventId'],
				'enabled' => 'T',
				'regTypeId' => $params['regTypeId']
			);
			
			$template = $this->queryUnique($sql, $p, 'Find email template by reg type id.');
		}
		
		return $template;
	}
	
	/**
	 * 
	 * @param array $params [eventId, regTypeIds, contactFieldId, enabled, fromAddress, bcc, subject, header, footer]
	 */
	public function createEmailTemplate($params) {
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
		
		$p = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'contactFieldId',
			'enabled',
			'fromAddress',
			'bcc',
			'subject',
			'header',
			'footer'
		));
		
		$this->execute($sql, $params, 'Create email template.');
		
		$emailTemplateId = $this->lastInsertId();
		
		// create reg type associations.
		$params['id'] = $emailTemplateId;
		$this->createRegTypeAssociation($params);
		
		return $emailTemplateId;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, regTypeIds, enabled, contactFieldId, fromAddress, bcc, subject, header, footer]
	 */
	public function save($params) {
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
			AND
				eventId = :eventId
		';
		
		$p = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'id',
			'contactFieldId',
			'enabled',
			'fromAddress',
			'bcc',
			'subject',
			'header',
			'footer'
		));
		
		$this->execute($sql, $p, 'Save email template.');
		
		$this->removeRegTypeAssociations(array(
			'eventId' => $params['eventId'],
			'emailTemplateId' => $params['id']
		));
		
		$this->createRegTypeAssociation(array(
			'eventId' => $params['eventId'],
			'emailTemplateId' => $params['id'],
			'regTypeIds' => $params['regTypeIds']
		));
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	private function isAvailableToAll($params) {
		$sql = '
			SELECT
				RegType_EmailTemplate.emailTemplateId
			FROM 
				RegType_EmailTemplate
			INNER JOIN
				EmailTemplate
			ON
				RegType_EmailTemplate.emailTemplateId = EmailTemplate.id
			WHERE
				RegType_EmailTemplate.emailTemplateId = :id
			AND
				EmailTemplate.eventId = :eventId
			AND
				RegType_EmailTemplate.regTypeId is NULL
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$result = $this->rawQueryUnique($sql, $params, 'Check if email template is visible to all reg types.');
		
		return !empty($result);
	}
	
	/**
	 * 
	 * @param array $params [eventId, emailTemplateId]
	 */
	private function removeRegTypeAssociations($params) {
		$sql = '
			DELETE FROM
				RegType_EmailTemplate
			WHERE
				RegType_EmailTemplate.emailTemplateId = :emailTemplateId
			AND
				RegType_EmailTemplate.emailTemplateId
			IN (
				SELECT EmailTemplate.id
				FROM EmailTemplate
				WHERE EmailTemplate.eventId = :eventId
			)
		';	
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'emailTemplateId'));
		
		$this->execute($sql, $params, 'Remove reg type associations for email template.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, emailTemplateId, regTypeIds]
	 */
	private function createRegTypeAssociation($params) {
		$this->checkEmailTemplatePermission($params);
		
		if(in_array(-1, $params['regTypeIds'])) {
			$sql = '
				INSERT INTO
					RegType_EmailTemplate(
						emailTemplateId
					)
				VALUES(
					:emailTemplateId
				)
			';
			
			$p = ArrayUtil::keyIntersect($params, array('emailTemplateId'));
			
			$this->execute($sql, $p, 'Set email template available to all reg types.');
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
			
			foreach($params['regTypeIds'] as $regTypeId) {
				$p = array(
					'regTypeId' => $regTypeId,
					'emailTemplateId' => $params['emailTemplateId']
				);	
				
				$this->execute($sql, $p, 'Create reg type associations for email template.');
			}
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, emailTemplateId]
	 */
	public function delete($params) {
		$this->removeRegTypeAssociations($params);
		
		$sql = '
			DELETE FROM
				EmailTemplate
			WHERE
				id = :emailTemplateId
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'emailTemplateId'));
		
		$this->execute($sql, $params, 'Delete email template.');
	}
	
	/**
	 * 
	 * @param array $params [eventId]
	 */
	public function deleteByEventId($params) {
		$templates = $this->findByEventId($params);
		foreach($templates as $t) {
			$this->delete(array(
				'eventId' => $params['eventId'],
				'emailTemplateId' => $t['id']
			));
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, emailTemplateIds]
	 */
	public function deleteTemplates($params) {
		// delete reg type associations.
		$sql = '
			DELETE FROM
				RegType_EmailTemplate
			WHERE
				emailTemplateId IN (
					SELECT
						id 
					FROM
						EmailTemplate
					WHERE
						eventId = :eventId
					AND
						id IN (:[emailTemplateIds])
				)
		';
		
		$p = ArrayUtil::keyIntersect($params, array('eventId', 'emailTemplateIds'));
		
		$this->execute($sql, $p, 'Delete email template reg type associations.');
		
		$sql = '
			DELETE FROM
				EmailTemplate
			WHERE
				eventId = :eventId
			AND
				id IN (:[emailTemplateIds])
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'emailTemplateIds'));
		
		$this->execute($sql, $params, 'Delete email templates.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, emailTemplateId]
	 */
	private function checkEmailTemplatePermission($params) {
		$sql = '
			SELECT
				id,
				eventId
			FROM
				EmailTemplate
			WHERE
				id = :emailTemplateId
			AND
				eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'emailTemplateId'));
		
		$results = $this->rawQuery($sql, $params, 'Check email template permission.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to EmailTemplate. (event id, email template id) -> ({$params['eventId']}, {$params['emailTemplateId']})");
		}
	}
}

?>