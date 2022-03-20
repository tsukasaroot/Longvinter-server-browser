<?php

namespace Core;

use Memcache;
use Redis;

class Caching
{
	private Memcache $memcached;
	private Redis $redis;
	
	public bool $memcache_status = false;
	public bool $redis_status = false;
	
	public function __destruct()
	{
		// TODO: Implement __destruct() method.
	}
	
	public function __construct()
	{
		if (isset($GLOBALS['MEMCACHED_HOST']) && $GLOBALS['MEMCACHED_HOST']) {
			$this->memcached = new Memcache();
			if ($this->memcached->addServer($GLOBALS['MEMCACHED_HOST'], $GLOBALS['MEMCACHED_PORT'])) {
				$this->memcache_status = true;
			}
		}
		
		if (isset($GLOBALS['REDIS_HOST']) && $GLOBALS['REDIS_HOST']) {
			$this->redis = new Redis();
			if ($this->redis->connect($GLOBALS['REDIS_HOST'], intval($GLOBALS['REDIS_PORT']))) {
				if ($GLOBALS['REDIS_PASSWORD']) {
					$this->redis->auth($GLOBALS['REDIS_PASSWORD']);
				}
				$this->redis_status = true;
			}
		}
	}
	
	public function add(array $array = null, string $key = null, string $value = null, string $method = null): bool
	{
		$success = false;
		
		if ($method === 'memcached') {
			if ($array) {
				foreach ($array as $k => $item) {
					$success = $this->memcached->add($k, $item);
				}
			}
			
			if ($key && $value) {
				$success = $this->memcached->add($key, $value);
			}
		}
		
		if ($method === 'redis') {
			if ($array) {
				foreach ($array as $k => $item) {
					$success = $this->memcached->set($k, $item);
				}
			}
			
			if ($key && $value) {
				$success = $this->memcached->set($key, $value);
			}
		}
		
		return $success;
	}
	
	public function get(array $array = null, string $key = null, string $method = null): array
	{
		$result_array = [];
		
		if ($method === 'memcached') {
			if ($array) {
				foreach ($array as $item) {
					$result_array[$item] = $this->memcached->get($item);
				}
			}
			
			if ($key)
				$result_array[$key] = $this->memcached->get($key);
		}
		
		if ($method === 'redis') {
			if ($array) {
				foreach ($array as $item) {
					$result_array[$item] = $this->redis->get($item);
				}
			}
			
			if ($key)
				$result_array[$key] = $this->redis->get($key);
		}
		
		return $result_array;
	}
	
	public function delete(array $array = null, string $key = null, string $method = null): bool
	{
		$success = false;
		
		if ($method === 'memcached') {
			if ($array) {
				foreach ($array as $item) {
					$success = $this->memcached->delete($item);
				}
			}
			
			if ($key)
				$success = $this->memcached->delete($key);
		}
		
		if ($method === 'redis') {
			if ($array) {
				foreach ($array as $item) {
					$success = $this->redis->del($item);
				}
			}
			
			if ($key)
				$success = $this->redis->del($key);
		}
		
		return $success;
	}
	
	public function flush(string $method = null)
	{
		if ($method === 'memcached')
			$this->memcached->flush();
		
		if ($method === 'redis')
			$this->redis->flushAll();
	}
}