<?php

namespace Core;

class Console
{
	public static function run(string $class_name, string $function, array $args = null)
	{
		$action[0] = 'App\\Console\\' . $class_name;
		
		$obj = new $action[0]();
		call_user_func([$obj, $function]);
	}
}