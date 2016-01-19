<?php
namespace FM;

class Input extends Component{
	public function __construct(){

	}

	/**
	 * Returns GET parameter. If $key isn't specified, returns an array of all POST parameters.
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @return null|string
	 */
	public function get($key = '', $default = ''){
		if (empty($key)){
			return $_GET;
		}

		if (isset ($_GET[$key])){
			return $_GET[$key];
		}

		if (!empty($default)){
			return $default;
		}

		return null;
	}

	/**
	 * Returns POST parameter. If $key isn't specified, returns an array of all POST parameters.
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @return null|string
	 */
	public function post($key = '', $default = ''){
		if (empty($key)){
			return $_POST;
		}

		if (isset ($_POST[$key])){
			return $_POST[$key];
		}

		if (!empty($default)){
			return $default;
		}

		return null;
	}

	/**
	 * Return HTTP method of request
	 *
	 * @return string
	 */
	public function method(){
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Detect if this Ajax request
	 * @return bool
	 */
	public function isAjax(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}
}