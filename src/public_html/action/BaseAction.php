<?php

class action_BaseAction implements action_Action
{
	private $logger;

	function __construct() {
		$this->logger = new Logger();
	}

	public function execute() {
		$action = $_REQUEST['action'];
		
		$this->performSecurityCheck($action);
		
		$this->performAction($action);
	}
	
	protected function strictFindById($manager, $id) {
		$obj = $manager->find($id);
		
		if(empty($obj)) {
			throw new Exception('Object does not exist: '.$id);
		}
		
		return $obj;
	}
	
	/**
	 * Map from restriction type to an array of method names on which the 
	 * restriction should be applied.
	 */
	protected function getSecurityConfig() {
		return array();	
	}
	
	/**
	 * invokes the method named by the 'action' request parameter.
	 */
	private function performAction($action) {
		if(!empty($action) && method_exists($this, $action)) {
			try {
				$page = call_user_func(array($this, $action));
				if(!($page instanceof template_Template)) {
					throw new Exception(get_class($page).' not of type Template.');
				}
			}
			catch(Exception $ex) {
				$this->logger->log($ex,'Error invoking action: '.$action);
				$page = new template_ErrorPage();
			}
		}
		else {
			$this->logger->log('Action not found: '.$action);
			$page = new template_ErrorPage();
		}

		echo $page->html();
	}
	
	private function performSecurityCheck($action) {
		$config = $this->getSecurityConfig();
		
		foreach($config as $type => $methods) {
			if(in_array($action, $methods)) {
				switch($type) {
					case security_Restriction::$EVENT:
						break;
					case security_Restriction::$USER:
						break;
				}
			}
		}
	}
}