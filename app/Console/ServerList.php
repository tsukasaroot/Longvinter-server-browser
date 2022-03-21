<?php

namespace App\Console;

use Core\Caching;

class ServerList
{
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