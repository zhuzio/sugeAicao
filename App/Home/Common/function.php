<?php
function returnstr($strlen) {
	$str = '';
	for ($i = 0; $i < $strlen; $i++) {
		$str .= '*';
	}
	return $str;
}
