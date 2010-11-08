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
		$className = implode('_', $this->uri);
		$path = implode('/', $this->uri);
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