<?php
namespace FM;

class URL extends Component{
	public function parts($index = null){
		$Parts = explode('/', ltrim(explode('?', $_SERVER['REQUEST_URI'])[0], '/'));

		$Parts['controller'] = isset($Parts[0]) ? $Parts[0] : null;
		$Parts['action'] = isset($Parts[1]) ? $Parts[1] : null;

		if (empty($index)){
			return $Parts;
		}

		return isset($Parts[$index]) ? $Parts[$index] : null;
	}
}