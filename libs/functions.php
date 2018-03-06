<?php

function __autoload($class)
{
	$class = strtolower($class);
	$files = glob(ABSPATH.'/*');
	$folders = array();
	foreach ($files as $key => $value) {
		if (!preg_match('/^.+(.php)$/', $value)) {
			$folders[] = basename($value);
		} else {
			unset($files[$key]);
		}
	}
	foreach ($folders as $key => $folder) {
		if (file_exists($folder.'/'.$class.'.php')) {
			include($folder.'/'.$class.'.php');
		}
	}
}

function clean($data)
{
	return stripslashes(strip_tags($data));
}

function enc($data)
{
return base64_encode($data.AUTH_KEY);
}

function dec($data)
{
return explode(AUTH_KEY, base64_decode($data))[0];
}
