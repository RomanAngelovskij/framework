<?php
namespace FM;

class Routes extends Component{
	private $__controller;

	private $__action;

	private $__currentRule;

	public function __construct(){
		$this->addEventListener();
	}

	/**
	 * Setup controller and action depending on the URL
	 */
	public function processRoutes(){
		$this->__processRoutesRules();

		$queryRouteUrl = Application::$i->Input->get('r');

		if (!empty ($queryRouteUrl)){
			$Parts = explode('/', $queryRouteUrl);
			$this->__controller = isset($Parts[0]) ? $Parts[0] : '';
			$this->__action = isset($Parts[1]) ? $Parts[1] : '';
		} else {
			$this->__controller = Application::$i->URL->parts('controller');
			$this->__action = Application::$i->URL->parts('action');
		}

		$this->__controller = $this->__getControllerName($this->__controller);
		$this->__action = $this->__getActionName($this->__action);
	}

	/**
	 * Returns controller name
	 *
	 * @return string
	 */
	public function controller(){
		return $this->__controller;
	}

	/**
	 * Returns action name
	 *
	 * @return string
	 */
	public function action(){
		return $this->__action;
	}

	/**
	 * Setup arguments for current action
	 *
	 * @param \ReflectionParameter $Parameter
	 *
	 * @return array
	 */
	public function fillParameter(\ReflectionParameter $Parameter){
		$UrlParts = Application::$i->URL->parts();
		if (!isset($UrlParts[$Parameter->getPosition()+2])){
			if ($Parameter->isDefaultValueAvailable() === false){
				showError('Parameter $' . $Parameter->getName() . ' not found');
			}

			$value = $Parameter->getDefaultValue();
		} else {
			$value = Application::$i->URL->parts($Parameter->getPosition()+2);
		}

		return $value;
	}

	/**
	 * If current URL present in routes rules in config, then use this rule
	 *
	 * @return bool
	 */
	private function __processRoutesRules(){
		if (!isset(Application::$i->Config['routes']['rules']) || empty(Application::$i->Config['routes']['rules'])){
			return false;
		}

		foreach (Application::$i->Config['routes']['rules'] as $rule => $r){
			if (trim($rule, '/') == trim($_SERVER['REQUEST_URI'], '/')){
				$this->__currentRule = $rule;
				$_GET['r'] = $r;
				return true;
			}
		}

		return false;
	}

	private function __getControllerName($name){
		if (empty($name)){
			$name = (isset(Application::$i->Config['main']['defaultController']) && !empty(Application::$i->Config['defaultController'])) ?
				Application::$i->Config['defaultController'] :
				'site';
		}

		return ucfirst(preg_replace('!-!', '', implode('-', array_map('ucfirst', explode('-', $name))))) . 'Controller';
	}

	private function __getActionName($name){
		if (empty($name)){
			$name = 'index';
		}

		return 'action' . ucfirst(preg_replace('!-!', '', implode('-', array_map('ucfirst', explode('-', $name)))));
	}
}