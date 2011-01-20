<?php

class StringUtil
{
	public static function isBlank($str) {
		return isset($str) && trim($str) === '';
	}
}

?>