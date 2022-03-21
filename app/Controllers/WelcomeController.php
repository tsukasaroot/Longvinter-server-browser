<?php

namespace App\Controllers;

use Core\Caching;
use App\Models\Servers;

class WelcomeController extends Controller
{
	public function home(): bool
	{
		$servers = new Servers();
		
		$servers_list = $servers->get_servers_list();
		$cleaned_list = [];
		
		for ($i = 0; $i < count($servers_list); $i++) {
			$cleaned_list[$servers_list[$i]['steamid']][] = $servers_list[$i]['addr'];
			$cleaned_list[$servers_list[$i]['steamid']][] = $servers_list[$i]['max_players'];
			$cleaned_list[$servers_list[$i]['steamid']][] = $servers_list[$i]['os'];
		}
		
		return $this->response(['servers_list' => $cleaned_list]);
	}
}