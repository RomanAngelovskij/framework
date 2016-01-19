<?php
namespace FM;

use FM\db\Connection;

class Application{

	public static $i;

	private static $__instance;

	private $__Config;

	private function __construct(){}

	final public function __clone()
	{
		throw new \Exception('This is a Singleton. Clone is forbidden');
	}

	public static function getInstance()
	{
		if (null === static::$__instance){
			static::$__instance = new static();
		}
		return static::$__instance;
	}

	public function run($Config){
		self::$i = new \stdClass();

		try{
			$this->__setupConfig($Config);
		} catch (\Exception $e){
			$e->getMessage();
		}

		self::$i->Input = new Input;
		self::$i->URL = new URL();
		self::$i->Routes = new Routes();
		self::$i->Views = new Views();

		self::$i->Routes->processRoutes();

		self::$i->dbConnection = new Connection(
			self::$i->Config['db']['dsn'],
			self::$i->Config['db']['username'],
			self::$i->Config['db']['password'],
			self::$i->Config['db']['charset']
		);

		self::$i->dbConnection->open();

		$this->__callAction();
	}

	private function __setupConfig($Config){
		if (!is_array($Config)){
			throw new \Exception('Invalid config');
		}

		self::$i->Config = $Config;
	}

	private function __callAction(){
		$controllerName = 'app\controllers\\' . self::$i->Routes->controller();
		$Controller = new $controllerName();

		$Reflection = new \ReflectionClass($Controller);
		try{
			$ActionReflection = $Reflection->getMethod(self::$i->Routes->action());
			$Parameters = $ActionReflection->getParameters();
		} catch (\ReflectionException $e) {
			throw new \Exception($e->getMessage());
		} catch (\Exception $e){
			throw new \Exception($e->getMessage());
		}

		if (!empty($Parameters)){
			foreach ($Parameters as $Parameter){
				$InvokeParameters[] = self::$i->Routes->fillParameter($Parameter);
			}

			$ActionReflection->invokeArgs($Controller, $InvokeParameters);
		} else {
			$ActionReflection->invoke($Controller);
		}
	}
}