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
	 * Performs any security related tasks. This method is called before the
	 * action is performed, and should throw an exception if any security 
	 * requirements are not met.
	 */
	protected function performSecurityCheck($action) {}
	
	/**
	 * invokes the method named by the 'action' request parameter.
	 */
	private function performAction($action) {
		if(!empty($action) && method_exists($this, $action)) {
			$page = call_user_func(array($this, $action));
			if(!($page instanceof template_Template)) {
				throw new Exception(get_class($page).' not of type Template.');
			}
		}
		else {
			$this->logger->log('Action not found: '.$action);
			$page = new template_ErrorPage();
		}

		echo $page->html();
	}
}