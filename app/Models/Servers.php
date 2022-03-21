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
			$actual_time = intval(str_pad(date('i'), 2, '0', STR_PAD_LEFT));
			$previous_time = intval(explode('_', $previous_query[0])[1]);
			if (count($previous_query) > 0 && $actual_time - $previous_time < 5) {
				$output = $redis->get(key: $previous_query[0], method: 'redis')[$previous_query[0]];
			} else {
				$masterkey = $GLOBALS['MASTERKEY'];
				
				$url = 'https://api.steampowered.com/IGameServersService/GetServerList/v1/?key=' . $masterkey . '&filter=%5Cappid%5C1635450&limit=9999';
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
				
				$redis->flush(method: 'redis');
				$key = 'serverRequest_' . date('i');
				$redis->add(key: $key, value: $output, method: 'redis');
			}
		}
		
		return json_decode($output, true)['response']['servers'];
	}
	
	public function cron_store_servers_list()
	{
		$redis = new Caching();
		
		if ($redis->redis_status) {
			$masterkey = $GLOBALS['MASTERKEY'];
			
			$url = 'https://api.steampowered.com/IGameServersService/GetServerList/v1/?key=' . $masterkey . '&filter=%5Cappid%5C1635450&limit=9999';
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			
			$redis->flush(method: 'redis');
			$key = 'serverRequest_' . date('i');
			$redis->add(key: $key, value: $output, method: 'redis');
		}
	}
}