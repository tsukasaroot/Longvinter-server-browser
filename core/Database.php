<?php

namespace Core;

use mysqli;

class Database
{
	private mysqli $sql;
	private int $init = 0;
	
	public function __destruct()
	{
		if ($this->init)
		$this->sql->close();
	}
	
	public function __construct($db)
	{
		$databases = [];
		$i = 0;
		foreach ($GLOBALS['Database'] as $k => $v) {
			if (false !== stripos($k, 'db')) {
				if ($k[2] > $i)
					$i++;
				$databases[$i][] = $v;
			}
		}
		
		if ($db > 0)
			$this->makeMysqlConnection($databases[$db]);
	}
	
	private function makeMysqlConnection(array $connection)
	{
		$this->sql = new mysqli($connection[1], $connection[3],
			$connection[4], $connection[2]);
		
		if ($this->sql->connect_error) {
			$this->sql->close();
			die("Connection failed: ");
		}
		$this->init++;
	}
	
	public function getSql(): mysqli|null
	{
		if ($this->init)
			return $this->sql;
		return null;
	}
}