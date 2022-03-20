<?php

namespace Core;

class Http
{
	public static function receivedInput()
	{
		if (empty($_POST) && $json = file_get_contents('php://input')) {
			$var = [];
			$json = json_decode($json);
			
			foreach ($json as $k => $v) {
				$var[$k] = $v;
			}
			$_POST = $var;
		}
	}
	
	public static function sendJson(mixed $data, int $code=200)
	{
		$data['time'] = Bench::endTime($GLOBALS['start']);
		
		header('Content-Length: ' . strlen(json_encode($data)));
		header('Content-Type: application/json; charset=utf-8');
		http_response_code($code);
		
		echo json_encode($data);
	}
}
