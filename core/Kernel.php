<?php

namespace Core;
class Kernel
{
	public static function web()
	{
		if (empty($_SERVER['REQUEST_METHOD'])) {
			echo 'nope';
			die();
		}
		
		$GLOBALS['start'] = Bench::startTime();
		
		$request = $_SERVER['REQUEST_URI'];
		
		$GLOBALS['Http'] = $request;
		
		$env = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/../.env');
		
		foreach ($env as $k => $v) {
			if (str_starts_with($k, 'db'))
				$GLOBALS['Database'][$k] = $v;
			else
				$GLOBALS[$k] = $v;
		}
		
		date_default_timezone_set($GLOBALS['timezone'] ?? 'Europe/Paris');
		
		error_reporting(intval($GLOBALS['debug']) ?? false);
		ini_set('display_errors', $GLOBALS['debug'] ?? false);
		
		$token = new Token();
		
		if ($token->activated) {
			$auth_is_activated = apache_request_headers()['Auth-Token'];
			$auth_is_activated = $auth_is_activated ?: apache_request_headers()['auth-token'];
			$token->checkToken($auth_is_activated ?: '');
		}
		
		Http::receivedInput();
		Routes::create();
	}
}