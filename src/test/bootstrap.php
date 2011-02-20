<?php

// add the test dir to the include path.
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

// add public_html to include path.
$dirs = explode('/', dirname(__FILE__));
array_pop($dirs);
$dirs[] = 'public_html';

set_include_path(get_include_path() . PATH_SEPARATOR . implode('/', $dirs));

// perform application setup.
require_once 'Reggie.php';

Reggie::setup();

?>