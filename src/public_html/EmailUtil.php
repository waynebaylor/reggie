<?php

class EmailUtil
{
	public static function send($m) {
		// set character encoding to UTF-8.
		mb_language('uni');
		mb_internal_encoding('UTF-8');
		
		// this encodes the subject and the message body.
		//
		// this does not encode email names. For example, the 'John Smith' in 
		// the following is the name: "John Smith <john.smith@business.com>)".
		mb_send_mail(
			$m['to'],
			$m['subject'],
			wordwrap($m['text'], 70),
			"From: {$m['from']}".PHP_EOL.
			"Reply-To: {$m['from']}".PHP_EOL.
			"Bcc: {$m['bcc']}".PHP_EOL.
			'MIME-Version: 1.0'.PHP_EOL.
			'Content-Type: text/html; charset=utf-8'.PHP_EOL
		);
	}
}

?>