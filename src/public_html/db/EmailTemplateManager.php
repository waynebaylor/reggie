<?php

class db_EmailTemplateManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'EmailTemplate';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_EmailTemplateManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				id,
				eventId,
				enabled,
				fromAddress,
				bcc,
				subject,
				header,
				footer
			FROM
				EmailTemplate
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find email template.');		
	}
	
	public function findByEvent($event) {
		$sql = '
			SELECT
				id,
				eventId,
				enabled,
				fromAddress,
				bcc,
				subject,
				header,
				footer
			FROM
				EmailTemplate
			WHERE
				eventId=:eventId
		';
		
		$params = array(
			'eventId' => $event['id']
		);
		
		return $this->queryUnique($sql, $params, 'Find email template by event.');
	}
	
	public function createEmailTemplate($eventId) {
		$sql = '
			INSERT INTO
				EmailTemplate(
					eventId,
					enabled
				)
			VALUES(
				:eventId,
				:enabled
			)
		';
		
		$params = array(
			'eventId' => $eventId,
			'enabled' => 'false'
		);
		
		$this->execute($sql, $params, 'Create email template.');
	}
	
	public function save($template) {
		$sql = '
			UPDATE
				EmailTemplate
			SET
				enabled=:enabled,
				fromAddress=:fromAddress,
				bcc=:bcc,
				subject=:subject,
				header=:header,
				footer=:footer
			WHERE
				id=:id
		';
		
		$params = $template;
		
		$this->execute($sql, $params, 'Save email template.');
	}
}

?>