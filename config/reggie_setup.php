<?php

$file = '';
$dir = '';

if(isset($_REQUEST['unzip'])) {
	$z = new ZipArchive();
	if($z->open($file) === true) {
		$z->extractTo($dir);
		$z->close();
	}
	else {
		echo 'Unzip failed.';
	}
}

?>