<?php
namespace FM;

class Component{

	public function __construct(){

	}

	public function __get($name){
		$getterName = 'get' . ucfirst($name);

		if (method_exists($this, $getterName)){
			return $this->$getterName();
		}

		if (method_exists($this, 'set' . $getterName)) {
			throw new \Exception('Getting write-only property: ' . get_class($this) . '::' . $name);
		} else {
			throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
		}
	}

	public function __set($name, $value){
		$setterMethod = 'set' . ucfirst($name);

		if (method_exists($this, $setterMethod)) {
			$this->$setterMethod($value);

			return;
		}

		if (method_exists($this, 'get' . $name)) {
			throw new \Exception('Setting read-only property: ' . get_class($this) . '::' . $name);
		} else {
			throw new \Exception('Setting unknown property: ' . get_class($this) . '::' . $name);
		}
	}

	public function __isset($name)
	{
		$getterMethod = 'get' . $name;
		if (method_exists($this, $getterMethod)) {
			return $this->$getterMethod() !== null;
		}

		return !empty($this->__Data[$name]);
	}

	public function __call($name, $params)
	{
		throw new \Exception('Calling unknown method: ' . get_class($this) . "::$name()");
	}

	final public function addEventListener(){

	}

	final public function removeEventListener(){

	}

	final public function event($event){

	}
}