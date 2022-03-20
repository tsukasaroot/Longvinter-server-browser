<?php

namespace Core;

use mysqli;
use mysqli_result;
class Token
{
	public bool $activated;
	private mysqli $driver;
	private Database $database;
	private Caching $cache;
	const TABLE = 'tokens';
	
	public function __construct()
	{
		$this->activated = $GLOBALS['authToken'] ?? false;
		if (!$this->activated)
			return;
		$this->database = new Database(1);
		$this->driver = $this->database->getSql();
		$table = self::TABLE;
		
		if ($this->driver->query("SHOW TABLES LIKE '$table'")->num_rows !== 1) {
			$sql = <<<EOF
			CREATE TABLE $table (
			    token VARCHAR(128) PRIMARY KEY UNIQUE NOT NULL,
			    created_at bigint(11) NOT NULL
			)
			EOF;
			if (!$this->driver->query($sql)) {
				echo "Error upon creating $table: " . $this->driver->error . "\n";
			}
		}
		
		$this->cache = new Caching();
	}
	
	public function checkToken(string $token): void
	{
		$result = $this->performCheck($token);
		
		if ($result === null)
			die();
		
		if (!is_array($result))
			$date = $result->fetch_assoc()['created_at'];
		else
			$date = $result[$token];
		
		if ($date < strtotime('-30 days')) {
			Http::sendJson(['error' => 'Token outdated, please renew it.'], 401);
			die();
		}
	}
	
	public function renewToken(string $token): void
	{
		if ($this->performCheck($token) === null)
			return;
		$table = self::TABLE;
		
		$this->driver->query("DELETE FROM $table WHERE token='$token'");
		$this->cache->delete(key: $token, method: 'memcached');
		
		$date = time();
		$token = uniqid(more_entropy: true);
		$sql = <<<EOF
			INSERT INTO tokens VALUES('$token',$date)
			EOF;
		if ($this->driver->query($sql)) {
			$this->cache->add(key: $token, value: $date, method: 'memcached');
			Http::sendJson(['success' => 'Token added with success', 'token' => $token]);
		} else {
			Http::sendJson(['error' => "Error happened when inserting into table token", 'error_msg' => $this->driver->error], 500);
		}
	}
	
	private function performCheck(string $token): null|bool|mysqli_result|array
	{
		if (!$this->activated)
			return null;
		if ($token === 'null') {
			Http::sendJson(['error' => 'Token empty'], 401);
			die();
		}
		
		if ($this->cache->memcache_status) {
			$t = $this->cache->get(key: $token, method: 'memcached');
			if (!$t[$token]) {
				Http::sendJson(['error' => "Token doesn't exist", 'error_msg' => 'Not found in Memcache: ' . $token], 404);
				die();
			}
		}
		
		$table = self::TABLE;
		$result = $this->driver->query("SELECT created_at FROM $table WHERE token='$token'");
		
		if ($result->num_rows !== 1) {
			Http::sendJson(['error' => "Token doesn't exist", 'error_msg' => $this->driver->error], 404);
			die();
		}
		
		return $result;
	}
}