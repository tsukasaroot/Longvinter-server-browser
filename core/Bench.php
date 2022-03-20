<?php

namespace Core;

class Bench
{
	public static function startTime(): string
	{
		return microtime(true);
	}
	
	public static function endTime(string $time_start): string
	{
		$time_end = microtime(true);
		return $time_end - $time_start;
	}
}