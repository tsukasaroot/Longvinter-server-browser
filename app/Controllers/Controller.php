<?php

namespace App\Controllers;

use Core\Http;

class Controller
{
	public array $request;
	
	public function __construct()
	{
		foreach ($_POST as $key => $item) {
			$this->request[filter_var($key, FILTER_SANITIZE_STRING)] = filter_var($item, FILTER_SANITIZE_STRING);
		}
	}
	
	public function response(array|string $arg, int $code=200): bool
	{
		Http::sendJson($arg, $code);
		return true;
	}
}