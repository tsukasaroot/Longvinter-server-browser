<?php

namespace App\Models;

use Core\Model;
use Core\Caching;

class Servers extends Model
{
	public function get_servers_list()
	{
		$redis = new Caching();
		$output = null;
		
		if ($redis->redis_status) {
			$previous_query = $redis->get_keys(key: '*', method: 'redis');
			$output = $redis->get(key: $previous_query[0], method: 'redis')[$previous_query[0]];
		}
		
		return json_decode($output, true)['response']['servers'];
	}
}