<?php

class Controller
{
	private $url;

	function __construct($uri) {
		// strip any parameters.
		$url = explode('?', $uri); 
		$url = '/'.trim($url[0], '/');
		
		// strip the context.
		if(strpos($url, Reggie::$CONTEXT) === 0) {
			$url = substr($url, strlen(Reggie::$CONTEXT));
		}
		
		// make sure url starts with a '/'.
		$url = '/'.$url;
		$url = str_replace('//', '/', $url);
		
		$this->url = $url;
	}

	public function run() {
		try {
			db_EventManager::getInstance()->beginTransaction();
			
			// execute the action based on the url.
			$this->invokeAction();
			
			db_EventManager::getInstance()->commitTransaction();
		}
		catch(Exception $ex) {
			Logger::log($ex, 'Error executing action: '.$this->getAction().' in class "'.get_class($this).'"');
			
			db_EventManager::getInstance()->rollbackTransaction();			
			
			$page = new template_ErrorPage();
			echo $page->html();
		}
		
	}

	private function invokeAction() {
		$_REQUEST['action'] = $this->getAction();
		
		// requests starting with '/event' are handled 
		// by the registration dispatcher.
		if($this->isRegRequest()) {
			session_name('reggieReg');
			session_start();
			
			$this->invokeRegistration();
		}
		else if($this->isAdminRequest()) {
			session_name('reggieAdmin');
			session_start();
			
			$this->invokeAdmin();
		}
		else if($this->isStaticPageRequest()) {
			// no session for static pages.
			$this->invokeStaticPage();
		}
		else {
			throw new Exception('Invalid action: '.$this->url);
		}
	}
	
	private function invokeStaticPage() {
		$segments = explode('/', ltrim($this->url, '/'));
		if(count($segments) >= 3) {
			$eventCode = $segments[1];
			$pageName = $segments[2];
			
			$pageController = new action_staticPage_Controller();
			$pageController->view(array(
				'eventCode' => $eventCode,
				'name' => $pageName
			));
		}
		else {
			throw new Exception('Invalid static page: '.$this->url);
		}
	}
	
	private function invokeRegistration() {
		$segments = explode('/', ltrim($this->url, '/'));
		$regDispatcher = new RegistrationDispatcher($segments);
		$action = $regDispatcher->getRegistrationAction();
		$action->execute();
	}

	private function invokeAdmin() {
		// if the user isn't logged in, then redirect to the login page. 
		//
		// but don't redirect if this is a login request. since the login request goes 
		// through this code path too, we want to make sure we don't redirect 
		// AGAIN and get into an infinite redirect loop.
		
		$user = SessionUtil::getUser();
		
		$loginRequest = strpos($this->url, '/admin/Login') === 0;

		if(empty($user) && !$loginRequest) {
			$redirect = new template_Redirect('/admin/Login?a=view');
			echo $redirect->html();
			return;
		}
		
		$this->invoke();
	}
	
	private function invoke() {
		$className = Reggie::actionClass($this->url);
			
		$action = new $className();
		$action->execute();
	}
	
	private function getAction() {
		//
		// there are two ways to indicate the method to execute:
		// 1) include it in the request - localhost/admin/dashboard/MainMenu?a=view
		// 2) DEPRECATED: include it in the request - localhost/admin/dashboard/MainMenu?action=view
		//
		
		if(isset($_REQUEST['action'])) {
			$method = $_REQUEST['action'];
		}
		else {
			$method = RequestUtil::getValue('a', 'view');
		}
		
		return $method;
	}
	
	private function isRegRequest() {
		return strpos($this->url, '/event') === 0;
	}
	
	private function isAdminRequest() {
		return strpos($this->url, '/admin') === 0;
	}
	
	private function isStaticPageRequest() {
		return strpos($this->url, '/pages') === 0;
	}
}

?>