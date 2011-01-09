<?php

class Logger
{
	/**
	 * Log a message/exception. Accepts 1 or 2 arguments of type
	 * String and/or Exception.
	 */
	public function log(/*Exception, string*/) {
		if(func_num_args() > 0) {
			if(func_num_args() === 1) {
				$var = func_get_arg(0);
				if($var instanceof Exception) {
					$this->logExceptionAndMessage($var,'');
				}	
				else {
					error_log($var);
				}
			}
			else if(func_num_args() === 2) {
				$var = func_get_arg(0);
				if($var instanceof Exception) {
					$ex = func_get_arg(0);
					$msg = func_get_arg(1);
					$this->logExceptionAndMessage($ex, $msg);
				}
				else {
					$ex = func_get_arg(1);
					$msg = func_get_arg(0);
					$this->logExceptionAndMessage($ex, $msg);
				}
			}
		}	
	}
	
	public function logSql(/*string*/ $sql, /*array*/ $params, $msg = '', /*boolean*/ $success) {
		// only log SQL in development mode.
		if(in_array(Config::$MODE_SHOW_SQL, Config::$SETTINGS['MODE'])) {
			$this->log($msg."\nSQL:\n".$sql."\nParameter Values:\n".print_r($params, true)."\nSuccess: ".($success? 'TRUE' : 'FALSE'));
		}
	}
	
	private function logExceptionAndMessage(/*Exception*/ $ex, /*string*/ $msg = '') {
		$s = $ex->getFile() . '[' . $ex->getLine() . ']: ' . $msg;
		if($msg) {
			$s .= "\n";
		}
		$s .= $ex->getMessage();
		
		error_log($s);
	}
	
	public function logPayment($info) {
		error_log('['.date('j M Y, H:i:s').'] '.$info.PHP_EOL, 3, Config::$SETTINGS['PAYMENT_LOG']);
	}
}

?>