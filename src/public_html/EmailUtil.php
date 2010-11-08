<?php

class EmailUtil
{
	public static function send($m) {
		mail(
			$m['to'],
			$m['subject'],
			wordwrap($m['text'], 70),
			"From: {$m['from']}".PHP_EOL.
			"Reply-To: {$m['from']}".PHP_EOL.
			"Bcc: {$m['bcc']}".PHP_EOL.
			'MIME-Version: 1.0'.PHP_EOL.
			'Content-type: text/html; charset=iso-8859-1'.PHP_EOL
		);
	}
}

?>