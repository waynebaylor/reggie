<?php

class db_CategoryManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Category';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_CategoryManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT 
				id,
				displayName
			FROM
				Category
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find category.');
	}
	
	public function findAll() {
		$sql = '
			SELECT 
				id,
				displayName
			FROM
				Category
		';
		
		return $this->query($sql, array(), 'Find all categories.');
	}
	
	public function findByPage($page) {
		$sql = '
			SELECT
				Category.id,
				Category.displayName
			FROM
				Category
			INNER JOIN
				CategoryPage
			ON
				CategoryPage.categoryId=Category.id
			WHERE
				CategoryPage.pageId=:pageId
		';
		
		$params = array(
			'pageId' => $page['id']
		);
		
		return $this->query($sql, $params, 'Find page categories.');
	}
	
	public function findByRegType($regType) {
		$sql = '
			SELECT
				Category.id,
				Category.displayName
			FROM
				Category
			INNER JOIN
				CategoryRegType
			ON
				CategoryRegType.categoryId=Category.id
			WHERE
				CategoryRegType.regTypeId=:id
		';
		
		$params = array(
			'id' => $regType['id']
		);
		
		return $this->query($sql, $params, 'Find reg type categories.');
	}
}

?>