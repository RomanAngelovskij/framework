<?php
namespace FM;

class BaseModel extends Component{
	private $__Variables = [];

	public function __construct(){

	}

	public function __get($key){
		return isset($this->__Variables[$key]) ? $this->__Variables[$key] : null;
	}

	public function __set($key, $val){
		$this->__Variables[$key] = $val;
	}

	public function __isset($name)
	{
		try {
			return $this->__get($name) !== null;
		} catch (\Exception $e) {
			return false;
		}
	}
}