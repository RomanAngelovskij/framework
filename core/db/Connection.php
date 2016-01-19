<?php
namespace FM\db;

class Connection{
	private  $__pdo;

	private $__dsn;

	private $__username;

	private $__password;

	private $__charset;

	private $__driver;

	private $__driverObj;

	public function __construct($dsn, $username, $password, $charset = 'utf8'){
		$this->__pdo = null;

		$this->__dsn = $dsn;
		$this->__username = $username;
		$this->__password = $password;
		$this->__charset = $charset;

		if (($pos = strpos($this->__dsn, ':')) !== false) {
			$this->__driver = strtolower(strtolower(substr($this->__dsn, 0, $pos)));
			$driver = 'FM\db\drivers\\' . $this->__driver;
			$this->__setupDriver(new $driver);
		}
	}

	public function open(){
		if ($this->__pdo !== null) {
			return;
		}

		try{
			$this->__pdo = new \PDO($this->__dsn, $this->__username, $this->__password);
			$this->__pdo->exec('SET NAMES ' . $this->__pdo->quote($this->__charset));
			unset($this->__password);
		} catch (\PDOException $e) {
			throw new \Exception('Can\'t connect to database');
		}
	}

	public function close(){
		$this->__pdo = null;
	}

	public function driver(){
		return $this->__driverObj;
	}

	public function pdo(){
		return $this->__pdo;
	}

	private function __setupDriver(drivers\DBDriverInterface $Driver){
		$this->__driverObj = $Driver;
	}
}