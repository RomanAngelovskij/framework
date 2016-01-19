<?php
namespace FM;

class BaseController extends Component{

	public function __construct(){

	}

	public function render($view, $Data = [], $useLayout = true){
		$content = Application::$i->Views->makeView($view, $Data, $useLayout, false);

		echo $content;
	}

	public function returnRender($view, $Data = [], $useLayout = true){
		$content = Application::$i->Views->makeView($view, $Data, $useLayout, true);

		return $content;
	}

	public function renderJSON($Data){
		header('Content-Type: application/json');
		echo json_encode($Data);
	}
}