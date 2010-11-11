<?php

/////////////////////////////////////////////////////
// Set up the environment.
/////////////////////////////////////////////////////
require_once 'Reggie.php';
Reggie::setup();

/////////////////////////////////////////////////////

class Controller
{
	private $logger;
	private $uri;

	function __construct($uri) {
		$this->logger = new Logger();

		$uri = explode('?', $uri);
		$uri = $uri[0];
		$this->uri = explode('/', trim($uri, '/'));
	}

	public function run() {
		// execute the action based on the url.
		$this->invokeAction();
	}

	// parse the uri
	private function invokeAction() {
		try {
			session_start();
			
			// requests starting with '/event' are handled 
			// by the registration dispatcher.
			if($this->uri[0] === 'event') {
				$this->invokeRegistration();
			}
			// eventually should be like '/admin/...'
			else if($this->uri[0] === 'admin') {
				$this->invokeAdmin();
			}
			else {
				$this->invoke();
			}
		}
		catch(Exception $ex) {
			$this->logger->log($ex, 'Could not execute action.');

			$page = new template_ErrorPage();
			echo $page->html();
		}
	}
	
	private function invokeRegistration() {
		$regDispatcher = new RegistrationDispatcher($this->uri);
		$action = $regDispatcher->getRegistrationAction();
		$action->execute();
	}

	private function invokeAdmin() {
		$this->invoke();
	}
	
	private function invoke() {
		//
		// there are three ways to indicate the method to execute:
		// 1) include it in the request - localhost/action/admin/MainMenu?a=view
		// 2) include it as part of the url - localhost/action/admin/MainMenu/view
		// 3) DEPRECATED: include it in the request - localhost/action/admin/MainMenu?action=view
		//
		
		if(isset($_REQUEST['a'])) {
			$method = $_REQUEST['a'];
			$className = implode('_', $this->uri);
		}
		else if(isset($_REQUEST['action'])) {
			$method = $_REQUEST['action'];
			$className = implode('_', $this->uri);
		}
		else {
			// the last segment is the name of the method to execute.
			$method = $this->uri[count($this->uri)-1];
			$className = implode('_', array_slice($this->uri, 0, -1));
		}
		
		$_REQUEST['action'] = $method;
		
		$path = implode('/', explode('_', $className));
		$file = $path.'.php';
		
		if(file_exists(Reggie::$PATH.'/'.$file)) {
			require_once $file;
			
			$action = new $className();
			$action->execute();
		}
		else {
			throw new Exception('Invalid URL. Script does not exist: '.$path);
		}
	}
}

///////////////////////////////////////////////////////////
// this is where everything starts.
/////////////////////////////////////////////////////////

// run the request.
$controller = new Controller($_SERVER['REQUEST_URI']);
$controller->run();

?>