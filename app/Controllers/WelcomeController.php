<?php

namespace App\Controllers;
use App\Models\Users;
use Core\Caching;

class WelcomeController extends Controller
{
	public function test_post(): bool
	{
		if (empty($this->request['welcome'])) {
			return $this->response(['error' => 'Argument not provided'], 404);
		}
		$user = new Users();
		$cache = new Caching();
		
		if ($cache->redis_status)
			$arg = $cache->get(key: 'test', method: 'redis');
		
		return $this->response(['message' => 'received', 'input' => $this->request['welcome'], 'redis' => $arg]);
	}
}