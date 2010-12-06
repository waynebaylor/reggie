<?php

class Controller
{
	private $logger;
	private $url;

	function __construct($uri) {
		$this->logger = new Logger();

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
		// execute the action based on the url.
		$this->invokeAction();
	}

	private function invokeAction() {
		try {
			session_start();
			
			$_REQUEST['action'] = $this->getAction();
			
			// requests starting with '/event' are handled 
			// by the registration dispatcher.
			if($this->isRegRequest()) {
				$this->invokeRegistration();
			}
			else if($this->isAdminRequest()) {
				$this->invokeAdmin();
			}
			else {
				throw new Exception('Invalid action: '.$this->url);
			}
		}
		catch(Exception $ex) {
			$this->logger->log($ex, 'Could not execute action.');

			$page = new template_ErrorPage();
			echo $page->html();
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
		
		$loginRequest = strpos($this->url, '/admin/Login');
		$loginRequest = $loginRequest !== false && $loginRequest === 0;

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
		// 1) include it in the request - localhost/admin/MainMenu?a=view
		// 2) DEPRECATED: include it in the request - localhost/admin/MainMenu?action=view
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
}

?>