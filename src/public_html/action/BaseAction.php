<?php

/**
 * 
 * Core Action class that performs the mapping between user requested 
 * action and method execution.
 * 
 * @author wtaylor
 *
 */
abstract class action_BaseAction
{
	function __construct() {}
	
	/**
	 * Check if user has valid role for executing action. 
	 * @param array $user the user
	 * @param integer $eventId the event id
	 * @param string $method the action method being executed
	 */
	public function checkRole($user, $eventId=0, $method='') {
		return true;
	}
	
	public function execute() {
		$action = $_REQUEST['action'];
		
		$this->performAction($action);
	}
	
	protected function strictFindById($manager, $id) {
		$obj = $manager->find($id);
		
		if(empty($obj)) {
			throw new Exception('Error in "'.get_class($this).'". Object does not exist: '.$id);
		}
		
		return $obj;
	}
	
	protected function getFileContents($name) {
		$file = str_replace('_', '/', $name).'.php';
		
		ob_start();
		require $file;
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	/**
	 * Invokes the method named by the 'action' request parameter.
	 */
	private function performAction($action) {
		if(!empty($action) && method_exists($this, $action)) {
			$page = call_user_func(array($this, $action));
			if(!($page instanceof template_Template)) {
				throw new Exception(get_class($page).' not of type Template.');
			}
		}
		else {
			throw new Exception('Action "'.$action.'" not found in class "'.get_class($this).'"');
		}

		echo $page->html();
	}
}