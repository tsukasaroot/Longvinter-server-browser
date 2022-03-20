<?php

use Core\Database;

class Migrator
{
	private Database $database;
	private mysqli $driver;
	const TABLE = 'migrate';
	
	public function __construct()
	{
		$this->database = new Database(1);
		$this->driver = $this->database->getSql();
		$table = self::TABLE;
		
		if ($this->driver->query("SHOW TABLES LIKE '$table'")->num_rows !== 1) {
			echo "\033[31mTable $table don't exist\033[0m\n";
			$sql = <<<EOF
			CREATE TABLE $table (
			    name VARCHAR(50) NOT NULL,
				created_at bigint(11) NOT NULL
			)
			EOF;
			if ($this->driver->query($sql)) {
				echo "\033[32mTable $table created successfully\033[0m\n";
			} else {
				echo "\033[31mError upon creating $table: " . $this->driver->error . "\033[0m\n";
			}
		}
	}
	
	public function doMigration()
	{
		$path = 'Database/migrations';
		$filesToMigrate = scandir($path);
		
		foreach ($filesToMigrate as $file) {
			if ($file === '.' || $file === '..')
				continue;
			if (explode('.', $file)[1] !== 'sql')
				continue;
			
			$time = filemtime($path . '/' . $file);
			$table = explode('.', $file)[0];
			$result = $this->driver->query("SELECT created_at FROM migrate WHERE name = '$table' AND created_at = $time");
			if (mysqli_num_rows($result) >= 1) {
				echo "\033[32m$file is already up to date in database \033[0m";
				continue;
			}
			
			$sql = file_get_contents($path . '/' . $file);
			$sql = explode("\n", $sql);
			$newSql = '';
			
			foreach ($sql as $line) {
				if (!str_starts_with($line, '--') && $line !== '' &&
					!str_starts_with($line, '/*')) {
					$newSql .= $line . "\n";
				}
			}
			if ($this->driver->query($newSql)) {
				echo "\033[32m$file migrated \033[0m \n";
				if (!$this->driver->query("INSERT INTO migrate(name, created_at)VALUES('$table', $time)")) {
					echo "\033[31mError by inserting into migrate table \033[0m";
				}
			} else {
				echo "\033[31mError migrating $file: " . $this->driver->error . " \033[0m \n";
			}
		}
	}
	
	public function doRefresh()
	{
		$path = 'Database/migrations';
		$filesToMigrate = scandir($path);
		$toDrop = [];
		
		foreach ($filesToMigrate as $file) {
			if ($file === '.' || $file === '..')
				continue;
			if (explode('.', $file)[1] !== 'sql')
				continue;
			$toDrop[] = explode('.', $file)[0];
		}
		
		$this->doDrop($toDrop);
		$this->doMigration();
	}
	
	public function doDrop(array $tables)
	{
		foreach ($tables as $table) {
			$sql = <<<EOF
			DROP TABLE $table
			EOF;
			
			if ($this->driver->query($sql)) {
				if (!$this->driver->query("DELETE FROM migrate WHERE name='$table'")) {
					echo "\033[31mError upon deleting $table:" . $this->driver->error . " \033[0m migration line \n";
				}
				echo "\033[33mTable $table DROP successfully \033[0m \n";
			} else {
				echo "\033[31mError upon deleting $table:" . $this->driver->error . " \033[0m \n";
			}
		}
	}
}
